# Configuration

## Network Configuration

WiPiNetbooter manages network config via three files installed to `/etc/network/`:

| File | Purpose |
|---|---|
| `interfaces` | Active config — loaded by the networking service |
| `interfaces.home` | Home WiFi template — applied when switching to Home mode |
| `interfaces.hotspot` | Hotspot template — applied when switching to Hotspot mode |

> **Warning:** These files use a fixed-line format. The comment `#DO NOT MOVE OR REMOVE LINES ABOVE`
> marks the boundary that the Python scripts depend on. Do not reorder lines above that comment.

### Static IP

To set a static IP for the Ethernet interface, use the **Network** page in the web UI
(Network → Static IP tab). The UI writes to `/etc/network/interfaces` and restarts networking.

To set manually, edit `/etc/network/interfaces`:

```
auto eth0
iface eth0 inet static
address 192.168.1.102
netmask 255.255.255.0
gateway 192.168.1.1
```

---

## Adding NetDIMM Boards

1. Open the web interface and go to **NetDIMMs**.
2. Click **Add NetDIMM**.
3. Enter a name and the board's IP address.
4. Save — the board will appear on the Dashboard with a live online/offline indicator.

NetDIMM config is stored in `/var/www/html/csv/dimms.csv`:

```
name,ipaddress,type
Main Cabinet,192.168.1.40,naomi
```

---

## ROM Directory Layout

Copy ROM files to `/boot/roms/` on the Pi. Both compressed and uncompressed formats are supported:

```
/boot/roms/
  mygame.bin
  anothergame.bin.gz
```

The game list is managed via the web UI (**Games → Edit Game List**) and stored in
`/var/www/html/csv/gamelist.csv`.

---

## Game List CSV Format

`/var/www/html/csv/gamelist.csv` columns:

```
system,filename,name,manufacturer,year,category,players,genre,favourite
naomi,mygame.bin,My Game,Sega,2001,action,2,fighting,0
```

| Column | Values |
|---|---|
| system | `naomi`, `naomi2`, `atomiswave` |
| filename | ROM filename in `/boot/roms/` |
| favourite | `0` or `1` |

Use **Games → Import CSV** to bulk-import a game list.

---

## State Files

WiPiNetbooter uses plain text files in `/sbin/piforce/` to persist mode settings:

| File | Values | Purpose |
|---|---|---|
| `wifimode.txt` | `wifioff`, `home`, `hotspot` | Active WiFi mode |
| `menumode.txt` | `simple`, `modern` | UI mode |
| `bootfile.txt` | `menu`, ROM filename | What to boot on startup |
| `emumode.txt` | `auto`, `manual` | Card emulator mode |
| `openmode.txt` | `openoff`, `openon` | OpenJVS on/off |
| `nfcmode.txt` | `nfcoff`, `nfcon` | NFC reader on/off |

These are written by the PHP web interface and read by the Python backend scripts.

---

<!-- TODO: Add card emulator hardware wiring details and serial port configuration -->
