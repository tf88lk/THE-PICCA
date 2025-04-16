Hardware Requirements
- Raspberry Pi Zero 2 W
- Raspberry Pi OS: Bullseye or Bookworm
- OLED SSD1306 Display (I2C)
- GPIO Button (GPIO19)
- LED (GPIO21)
- CSI Camera + libcamera (Picamera2)
  
1. System Preparation
   
sudo apt update
sudo apt full-upgrade -y
sudo reboot

3. Package Installation
   
sudo apt install -y \
python3-pip \
python3-picamera2 \
python3-gpiozero \
python3-pil \
python3-numpy \
python3-adafruit-blinka \
ffmpeg \
libavcodec-dev libavformat-dev libavdevice-dev \
libavfilter-dev libavutil-dev libswscale-dev

4. Install SSD1306 Driver
   
sudo pip3 install adafruit-circuitpython-ssd1306 --break-system-packages

5. Camera Test
   
libcamera-still -o test.jpg

6. Directory Structure
   
/home/YOUR-USERNAME/img/main.py

7. System Service File
   
sudo nano /etc/systemd/system/oledcam.service

[Unit]
Description=OLED Camera Monitor Service
After=network.target
[Service]
ExecStart=/usr/bin/python3 /home/YOUR-USERNAME/img/main.py
WorkingDirectory=/home/YOUR-USERNAME/img
Restart=always
User=YOUR-USERNAME
Environment=PYTHONUNBUFFERED=1
[Install]
WantedBy=multi-user.target

8. Start the Service
   
sudo systemctl daemon-reexec
sudo systemctl daemon-reload
sudo systemctl enable oledcam.service
sudo systemctl start oledcam.service
sudo systemctl status oledcam.service

10. Diagnostics
    
journalctl -u oledcam.service -b -n 50 --no-pager

11. Common Errors and Solutions
    
- board: sudo apt install python3-adafruit-blinka
- adafruit_ssd1306: sudo pip3 install adafruit-circuitpython-ssd1306 --break-system-packages
- gpiozero: sudo apt install python3-gpiozero
- picamera2: sudo apt install python3-picamera2
- /dev/i2c*: sudo usermod -aG i2c YOUR-USERNAME && reboot
- Camera not found: Check the CSI ribbon cable and try libcamera-still


Final Result
- OLED shows stats
- Button takes a photo
- LED blinks
- OLED shows "Photo saved!" after capturing
