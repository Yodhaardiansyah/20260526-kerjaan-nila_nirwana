#include <WiFi.h>
#include <WiFiManager.h>
#include <PubSubClient.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <OneWire.h>
#include <DallasTemperature.h>

LiquidCrystal_I2C lcd(0x27, 16, 2);

// ====================================================================
// ----- KONEKSI & TOPIK MQTT -----
// ====================================================================
const char* mqtt_server   = "103.93.132.90"; 
const int mqtt_port       = 1883;
const char* mqtt_user     = "bartoo";
const char* mqtt_pass     = "barto12345";

const char* TOPIC_PH      = "barto/ta/alat1/ph";
const char* TOPIC_TEMP    = "barto/ta/alat1/temp";
const char* TOPIC_WATER   = "barto/ta/alat1/water";
const char* TOPIC_RELAY1  = "barto/ta/alat1/relay1";
const char* TOPIC_RELAY2  = "barto/ta/alat1/relay2";

const char* TOPIC_CMD_RELAY1  = "barto/ta/alat1/cmd/relay1";
const char* TOPIC_CMD_RELAY2  = "barto/ta/alat1/cmd/relay2";
const char* TOPIC_CMD_RESTART = "barto/ta/alat1/cmd/restart";

// ====================================================================
// ----- Definisi Pin Hardware -----
#define PIN_PH 34
#define PIN_TEMP 4
#define PIN_RELAY1 16
#define PIN_RELAY2 17
#define PIN_WATER 18

OneWire oneWire(PIN_TEMP);
DallasTemperature sensors(&oneWire);

WiFiClient espClient;
PubSubClient client(espClient);

unsigned long lastMQTTCheck = 0;
unsigned long lastSample = 0;
unsigned long lastUpdate = 0;

float sumVoltage = 0;
int sampleCount = 0;
float avgVoltage = 0;
float ph = 0;
float temp = 0;

// ----- Median Filter -----
float readPHVoltage() {
  int buf[10];
  for (int i = 0; i < 10; i++) {
    buf[i] = analogRead(PIN_PH);
    delay(5);
  }
  for (int i = 0; i < 9; i++) {
    for (int j = i + 1; j < 10; j++) {
      if (buf[i] > buf[j]) {
        int t = buf[i]; buf[i] = buf[j]; buf[j] = t;
      }
    }
  }
  float adc = 0;
  for (int i = 2; i < 8; i++) { adc += buf[i]; }
  adc /= 6.0;
  return adc * (3.3 / 4095.0);
}

// ----- Callback MQTT -----
void mqttCallback(char* topic, byte* payload, unsigned int length) {
  String msg = "";
  for (int i = 0; i < length; i++) { msg += (char)payload[i]; }
  
  if (String(topic) == TOPIC_CMD_RELAY1) {
    if (msg == "ON" || msg == "on") digitalWrite(PIN_RELAY1, LOW); 
    else if (msg == "OFF" || msg == "off") digitalWrite(PIN_RELAY1, HIGH);
  }
  else if (String(topic) == TOPIC_CMD_RELAY2) {
    if (msg == "ON" || msg == "on") digitalWrite(PIN_RELAY2, LOW);
    else if (msg == "OFF" || msg == "off") digitalWrite(PIN_RELAY2, HIGH);
  }
  else if (String(topic) == TOPIC_CMD_RESTART) {
    if (msg == "RESTART" || msg == "restart") {
      digitalWrite(PIN_RELAY1, HIGH);
      digitalWrite(PIN_RELAY2, HIGH);
      lcd.clear(); lcd.setCursor(0, 0); lcd.print("Restarting...");
      delay(1500); 
      ESP.restart(); 
    }
  }
}

// ----- Non-Blocking MQTT Reconnect -----
void handleMQTT() {
  if (!client.connected()) {
    if (millis() - lastMQTTCheck >= 5000) {
      lastMQTTCheck = millis();
      String clientId = "ESP32Client-" + String(random(0xffff), HEX);
      
      // Menghubungkan ke MQTT dengan Username dan Password
      if (client.connect(clientId.c_str(), mqtt_user, mqtt_pass)) {
        client.subscribe(TOPIC_CMD_RELAY1);
        client.subscribe(TOPIC_CMD_RELAY2);
        client.subscribe(TOPIC_CMD_RESTART);
      }
    }
  } else {
    client.loop();
  }
}

void setup() {
  Serial.begin(115200);

  pinMode(PIN_RELAY1, OUTPUT);
  pinMode(PIN_RELAY2, OUTPUT);
  digitalWrite(PIN_RELAY1, HIGH);
  digitalWrite(PIN_RELAY2, HIGH);
  pinMode(PIN_WATER, INPUT_PULLUP);

  Wire.begin(21, 22);
  lcd.init(); lcd.backlight();
  
  WiFiManager wm;
  lcd.print("Konek AP: ALAT_1");
  if(!wm.autoConnect("ALAT_1")) ESP.restart(); 
  
  lcd.clear(); lcd.print("Memulai Alat...");
  delay(2000);
  
  sensors.begin();
  client.setServer(mqtt_server, mqtt_port);
  client.setCallback(mqttCallback);
}

void loop() {
  handleMQTT();

  // ==============================================================
  // 🚀 REFLEKS HARDWARE (SAFETY CUT-OFF ANTI BANJIR)
  // Berjalan seketika tanpa jeda waktu.
  // Jika Pompa Isi (R2) sedang ON (LOW) dan Sensor Air Penuh (1)
  // ==============================================================
  if (digitalRead(PIN_RELAY2) == LOW && digitalRead(PIN_WATER) == 1) {
    digitalWrite(PIN_RELAY2, HIGH); // LANGSUNG MATIKAN POMPA
    
    // Segera kirim update kilat ke MQTT agar web & Node-RED tahu
    if (client.connected()) {
      client.publish(TOPIC_WATER, "1");
      client.publish(TOPIC_RELAY2, "OFF");
    }
    Serial.println("[SAFETY] Air Penuh! Pompa Pengisian (R2) Dimatikan Seketika.");
  }

  // ===== Sampling pH tiap 100ms =====
  if (millis() - lastSample >= 100) {
    lastSample = millis();
    sumVoltage += readPHVoltage();
    sampleCount++;
  }

  // ===== Update Data Rutin tiap 10 detik =====
  if (millis() - lastUpdate >= 10000) {
    lastUpdate = millis();

    sensors.requestTemperatures();
    temp = sensors.getTempCByIndex(0);
    avgVoltage = sumVoltage / sampleCount;

    // Kalibrasi pH
    float v4 = 3.156, v7 = 2.521, v9 = 2.123;
    if (avgVoltage > v7) ph = 6.86 - ((avgVoltage - v7) * (6.86 - 4.01) / (v4 - v7));
    else ph = 6.86 + ((v7 - avgVoltage) * (9.18 - 6.86) / (v7 - v9));

    int waterState = digitalRead(PIN_WATER); 
    String r1Status = (digitalRead(PIN_RELAY1) == LOW) ? "ON" : "OFF";
    String r2Status = (digitalRead(PIN_RELAY2) == LOW) ? "ON" : "OFF";

    if (client.connected()) {
      char payload[10];
      dtostrf(ph, 1, 2, payload);
      client.publish(TOPIC_PH, payload);
      
      dtostrf(temp, 1, 1, payload);
      client.publish(TOPIC_TEMP, payload);
      
      client.publish(TOPIC_WATER, String(waterState).c_str());
      client.publish(TOPIC_RELAY1, r1Status.c_str()); 
      client.publish(TOPIC_RELAY2, r2Status.c_str()); 
    }

    // LCD Tampilan
    lcd.clear();
    lcd.setCursor(0,0);
    lcd.print("pH:"); lcd.print(ph, 2);
    lcd.print(" W:"); lcd.print(waterState); 
    lcd.setCursor(0,1);
    lcd.print("R1:"); lcd.print(r1Status == "ON" ? "1" : "0");
    lcd.print(" R2:"); lcd.print(r2Status == "ON" ? "1" : "0");
    lcd.print(" T:"); lcd.print(temp, 1);

    sumVoltage = 0; sampleCount = 0;
  }
}