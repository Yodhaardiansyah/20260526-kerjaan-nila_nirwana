import tkinter as tk
from tkinter import ttk, filedialog, messagebox
import psutil
import subprocess
import json
import os
import signal

CONFIG = "services.json"

# =========================
# CONFIG
# =========================

def load_items():
    if os.path.exists(CONFIG):
        with open(CONFIG, "r") as f:
            return json.load(f)
    return []

def save_items():
    with open(CONFIG, "w") as f:
        json.dump(items, f, indent=2)

items = load_items()
processes = {}

# =========================
# STATUS
# =========================

def get_service_status(name):
    try:
        s = psutil.win_service_get(name)
        return s.status()
    except:
        return "unknown"

def get_exe_status(path):
    for p in psutil.process_iter(['exe']):
        try:
            if p.info['exe'] and p.info['exe'].lower() == path.lower():
                return "running"
        except:
            pass
    return "stopped"

# =========================
# CONTROL
# =========================

def start_item(item):
    if item["type"] == "service":
        subprocess.run(
            ["sc", "start", item["name"]],
            shell=True
        )
    else:
        try:
            cmd = [item["path"]]

            args = item.get("args", "")
            if args:
                cmd += args.split()

            startupinfo = None

            if item.get("hidden"):
                startupinfo = subprocess.STARTUPINFO()
                startupinfo.dwFlags |= subprocess.STARTF_USESHOWWINDOW

            proc = subprocess.Popen(
                cmd,
                startupinfo=startupinfo
            )

            processes[item["path"]] = proc

        except Exception as e:
            messagebox.showerror("Error", str(e))

    root.after(500, refresh_cards)

def stop_item(item):
    if item["type"] == "service":
        subprocess.run(
            ["sc", "stop", item["name"]],
            shell=True
        )
    else:
        for p in psutil.process_iter(['pid', 'exe']):
            try:
                if (
                    p.info['exe']
                    and p.info['exe'].lower()
                    == item["path"].lower()
                ):
                    p.kill()
            except:
                pass

    root.after(500, refresh_cards)

def restart_item(item):
    stop_item(item)
    root.after(
        1500,
        lambda: start_item(item)
    )

# =========================
# DELETE
# =========================

def delete_item(item):
    if messagebox.askyesno(
        "Hapus",
        f"Hapus {item['name']}?"
    ):
        items.remove(item)
        save_items()
        refresh_cards()

# =========================
# CARD UI
# =========================

def refresh_cards():
    for widget in card_frame.winfo_children():
        widget.destroy()

    for item in items:

        card = tk.Frame(
            card_frame,
            bg="white",
            bd=1,
            relief="solid"
        )
        card.pack(
            fill="x",
            padx=10,
            pady=5
        )

        title = tk.Label(
            card,
            text=item["name"],
            font=("Segoe UI", 12, "bold"),
            bg="white"
        )
        title.pack(
            anchor="w",
            padx=10,
            pady=(8,0)
        )

        if item["type"] == "service":
            status = get_service_status(item["name"])
            detail = "Windows Service"
        else:
            status = get_exe_status(item["path"])
            detail = item["path"]

        tk.Label(
            card,
            text=f"Status: {status}",
            bg="white"
        ).pack(anchor="w", padx=10)

        tk.Label(
            card,
            text=detail,
            fg="gray",
            bg="white"
        ).pack(anchor="w", padx=10)

        btns = tk.Frame(card, bg="white")
        btns.pack(
            anchor="w",
            padx=10,
            pady=8
        )

        tk.Button(
            btns,
            text="Start",
            command=lambda i=item: start_item(i)
        ).pack(side="left", padx=2)

        tk.Button(
            btns,
            text="Stop",
            command=lambda i=item: stop_item(i)
        ).pack(side="left", padx=2)

        tk.Button(
            btns,
            text="Restart",
            command=lambda i=item: restart_item(i)
        ).pack(side="left", padx=2)

        tk.Button(
            btns,
            text="Delete",
            command=lambda i=item: delete_item(i)
        ).pack(side="left", padx=2)

# =========================
# ADD EXE WINDOW
# =========================

def add_exe_window():

    exewin = tk.Toplevel(root)
    exewin.title("Tambah Program")
    exewin.geometry("450x350")

    name_var = tk.StringVar()
    path_var = tk.StringVar()
    arg_var = tk.StringVar()
    auto_var = tk.BooleanVar()
    hidden_var = tk.BooleanVar()

    tk.Label(
        exewin,
        text="Nama"
    ).pack(anchor="w", padx=10)

    tk.Entry(
        exewin,
        textvariable=name_var
    ).pack(fill="x", padx=10)

    tk.Label(
        exewin,
        text="Path Program"
    ).pack(anchor="w", padx=10, pady=(10,0))

    frame = tk.Frame(exewin)
    frame.pack(fill="x", padx=10)

    tk.Entry(
        frame,
        textvariable=path_var
    ).pack(side="left", fill="x", expand=True)

    def browse():
        p = filedialog.askopenfilename(
            filetypes=[
                ("Program", "*.exe *.bat"),
                ("All", "*.*")
            ]
        )

        if p:
            path_var.set(p)

            if not name_var.get():
                name_var.set(
                    os.path.basename(p)
                )

    tk.Button(
        frame,
        text="Browse",
        command=browse
    ).pack(side="left", padx=5)

    tk.Label(
        exewin,
        text="Arguments"
    ).pack(anchor="w", padx=10, pady=(10,0))

    tk.Entry(
        exewin,
        textvariable=arg_var
    ).pack(fill="x", padx=10)

    tk.Checkbutton(
        exewin,
        text="Auto Start",
        variable=auto_var
    ).pack(anchor="w", padx=10, pady=5)

    tk.Checkbutton(
        exewin,
        text="Run Hidden",
        variable=hidden_var
    ).pack(anchor="w", padx=10)

    def save_exe():

        if not path_var.get():
            return

        items.append({
            "type": "exe",
            "name": name_var.get(),
            "path": path_var.get(),
            "args": arg_var.get(),
            "auto": auto_var.get(),
            "hidden": hidden_var.get()
        })

        save_items()
        refresh_cards()
        exewin.destroy()

    tk.Button(
        exewin,
        text="Tambah Program",
        command=save_exe
    ).pack(pady=15)

# =========================
# ADD WINDOW
# =========================

def open_add():

    win = tk.Toplevel(root)
    win.title("Tambah Item")
    win.geometry("500x500")

    tk.Label(
        win,
        text="Search Service"
    ).pack(pady=5)

    search_var = tk.StringVar()

    listbox = tk.Listbox(win)
    listbox.pack(
        fill="both",
        expand=True,
        padx=10,
        pady=5
    )

    services = []

    for s in psutil.win_service_iter():
        try:
            services.append(s.name())
        except:
            pass

    services.sort()

    def update_list(*args):
        listbox.delete(0, tk.END)

        q = search_var.get().lower()

        for svc in services:
            if q in svc.lower():
                listbox.insert(tk.END, svc)

    search_var.trace_add("write", update_list)

    tk.Entry(
        win,
        textvariable=search_var
    ).pack(fill="x", padx=10)

    update_list()

    def add_service():

        sel = listbox.curselection()

        if not sel:
            return

        svc = listbox.get(sel[0])

        items.append({
            "type": "service",
            "name": svc
        })

        save_items()
        refresh_cards()
        win.destroy()

    tk.Button(
        win,
        text="Tambah Service",
        command=add_service
    ).pack(pady=5)

    ttk.Separator(win).pack(
        fill="x",
        pady=10
    )

    tk.Button(
        win,
        text="Tambah Program EXE/BAT",
        command=add_exe_window
    ).pack(pady=10)

# =========================
# AUTO START
# =========================

def autostart_items():
    for item in items:
        if (
            item["type"] == "exe"
            and item.get("auto")
        ):
            start_item(item)

# =========================
# MAIN
# =========================

root = tk.Tk()
root.title("Mini Service Manager")
root.geometry("750x600")
root.configure(bg="#ececec")

top = tk.Frame(root, bg="#ececec")
top.pack(fill="x")

tk.Label(
    top,
    text="Mini Service Manager",
    font=("Segoe UI", 16, "bold"),
    bg="#ececec"
).pack(
    side="left",
    padx=10,
    pady=10
)

tk.Button(
    top,
    text="+ Add",
    command=open_add
).pack(
    side="right",
    padx=10
)

card_frame = tk.Frame(
    root,
    bg="#ececec"
)
card_frame.pack(
    fill="both",
    expand=True
)

refresh_cards()

root.after(
    1000,
    autostart_items
)

root.mainloop()