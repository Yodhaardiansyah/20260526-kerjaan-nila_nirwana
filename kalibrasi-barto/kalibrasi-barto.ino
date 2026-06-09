#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <OneWire.h>
#include <DallasTemperature.h>

LiquidCrystal_I2C lcd(0x27, 16, 2);

#define PIN_PH 34
#define PIN_TEMP 4

OneWire oneWire(PIN_TEMP);
DallasTemperature sensors(&oneWire);

// ----- Variabel averaging -----
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

  // sort
  for (int i = 0; i < 9; i++) {
    for (int j = i + 1; j < 10; j++) {
      if (buf[i] > buf[j]) {
        int t = buf[i];
        buf[i] = buf[j];
        buf[j] = t;
      }
    }
  }

  // ambil tengah (median average)
  float adc = 0;
  for (int i = 2; i < 8; i++) {
    adc += buf[i];
  }

  adc /= 6.0;

  return adc * (3.3 / 4095.0);
}

void setup() {
  Serial.begin(115200);

  Wire.begin(21, 22);

  lcd.init();
  lcd.backlight();
  lcd.clear();

  sensors.begin();

  lcd.setCursor(0,0);
  lcd.print("Init Sensor...");
}

void loop() {

  // ===== Sampling tiap 100ms =====
  if (millis() - lastSample >= 100) {
    lastSample = millis();

    float v = readPHVoltage();

    sumVoltage += v;
    sampleCount++;
  }

  // ===== Update tiap 10 detik =====
  if (millis() - lastUpdate >= 10000) {
    lastUpdate = millis();

    sensors.requestTemperatures();
    temp = sensors.getTempCByIndex(0);

    avgVoltage = sumVoltage / sampleCount;

    // ===== KALIBRASI PH BARU =====
    // Menggunakan metode Interpolasi Linear 2 Titik 
    // (Berdasarkan data V: 4.01=3.156V, 6.86=2.521V, 9.18=2.123V)
    
    float v4 = 3.156;
    float v7 = 2.521;
    float v9 = 2.123;
    
    if (avgVoltage > v7) {
      // Rentang Asam (pH 4.01 sampai 6.86)
      ph = 6.86 - ((avgVoltage - v7) * (6.86 - 4.01) / (v4 - v7));
    } else {
      // Rentang Basa (pH 6.86 sampai 9.18)
      // Tanda minus diganti menjadi plus (+) di bawah ini
      ph = 6.86 + ((v7 - avgVoltage) * (9.18 - 6.86) / (v7 - v9));
    }

    /* 
    // Jika ingin tetap memakai rumus kuadrat (polinomial), 
    // hapus komentar di bawah ini dan beri komentar pada blok if/else di atas:
    // ph = (1.298 * avgVoltage * avgVoltage) - (11.858 * avgVoltage) + 28.503;
    */

    // Serial
    Serial.print("AVG V: ");
    Serial.print(avgVoltage, 3);
    Serial.print(" Temp: ");
    Serial.print(temp, 1);
    Serial.print(" pH: ");
    Serial.println(ph, 2);

    // LCD
    lcd.clear();

    lcd.setCursor(0,0);
    lcd.print("pH:");
    lcd.print(ph,2);
    lcd.print(" T:");
    lcd.print(temp,1);
    lcd.print((char)223);
    lcd.print("C");

    lcd.setCursor(0,1);
    lcd.print("V:");
    lcd.print(avgVoltage,2);

    // reset averaging
    sumVoltage = 0;
    sampleCount = 0;
  }
}