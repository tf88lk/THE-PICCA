import time
import subprocess
import os
from datetime import datetime
from time import sleep
from threading import Thread

import board
import busio
from PIL import Image, ImageDraw, ImageFont
import adafruit_ssd1306
from gpiozero import Button, LED, OutputDevice
from signal import pause
from picamera2 import Picamera2
from libcamera import ColorSpace
from PIL import Image as PILImage

picam2 = Picamera2()
button = Button(19)
led = LED(21)

capture_config = picam2.create_still_configuration(
    main={"format": "BGR888"},
    colour_space=ColorSpace.Srgb()
)

picam2.configure(capture_config)

oled_reset = OutputDevice(4, active_high=False)
WIDTH = 128
HEIGHT = 64
LOOPTIME = 0.5
i2c = board.I2C()

oled_reset.on()
time.sleep(0.1)
oled_reset.off()
time.sleep(0.1)
oled_reset.on()

oled = adafruit_ssd1306.SSD1306_I2C(WIDTH, HEIGHT, i2c, addr=0x3C)
oled.fill(0)
oled.show()

image = Image.new('1', (WIDTH, HEIGHT))
draw = ImageDraw.Draw(image)

font_path = os.path.join(os.path.dirname(__file__), 'PixelOperator.ttf')
font = ImageFont.truetype(font_path, 16)

font_path = os.path.join(os.path.dirname(__file__), 'lineawesome-webfont.ttf')
icon_font = ImageFont.truetype(font_path, 18)

show_photo_message = False
file_path = ""
timestamp = ""
file_size_mb = 0.0

def capture():
    """ Robi zdjęcie i informuje o tym na OLED """
    global show_photo_message, file_path, timestamp, file_size_mb

    timestamp = datetime.now().isoformat()
    picam2.start()
    time.sleep(2)
    
    file_path = f'/home/tf88lk/img/images/{timestamp}.jpg'
    picam2.capture_file(file_path)
    picam2.stop()

    file_size = os.path.getsize(file_path)
    file_size_kb = file_size / 1024
    file_size_mb = file_size_kb / 1024

    image_pil = PILImage.open(file_path)
    image_pil = image_pil.rotate(180)
    image_pil.save(file_path)

    led.on()
    sleep(1)
    led.off()

    show_photo_message = True
    time.sleep(3)
    show_photo_message = False

def oled_loop():
    while True:
        draw.rectangle((0, 0, WIDTH, HEIGHT), outline=0, fill=0)

        if show_photo_message:
            draw.text((10, 10), f"Zdjecie zapisane!", font=font, fill=255)
            draw.text((10, 30), f"Czas: {timestamp}", font=font, fill=255)
            draw.text((10, 50), f"Rozmiar: {file_size_mb:.2f} MB", font=font, fill=255)
        else:
            IP = subprocess.check_output("hostname -I | cut -d' ' -f1 | head --bytes -1", shell=True).decode().strip()
            CPU = subprocess.check_output("top -bn1 | grep 'Cpu(s)' | sed \"s/.*, *\([0-9.]*\)%* id.*/\\1/\" | awk '{print 100 - $1 \"%\"}'", shell=True).decode().strip()
            MemUsage = subprocess.check_output("free -m | awk 'NR==2{printf \"%.2f%%\", $3*100/$2 }'", shell=True).decode().strip()
            Disk = subprocess.check_output("df -h | awk '$NF==\"/\"{printf \"%d/%dGB\", $3,$2}'", shell=True).decode().strip()
            Temperature = subprocess.check_output("vcgencmd measure_temp | cut -d '=' -f 2 | head --bytes -1", shell=True).decode().strip()

            draw.text((0, 5), chr(62609), font=icon_font, fill=255)  # Ikona temperatury
            draw.text((65, 5), chr(62776), font=icon_font, fill=255)  # Ikona RAM
            draw.text((0, 25), chr(63426), font=icon_font, fill=255)  # Ikona dysku
            draw.text((65, 25), chr(62171), font=icon_font, fill=255)  # Ikona CPU
            draw.text((0, 45), chr(61931), font=icon_font, fill=255)  # Ikona WiFi

            draw.text((20, 5), Temperature, font=font, fill=255)
            draw.text((87, 5), MemUsage, font=font, fill=255)
            draw.text((20, 25), Disk, font=font, fill=255)
            draw.text((87, 25), CPU, font=font, fill=255)
            draw.text((20, 45), IP, font=font, fill=255)

        oled.image(image)
        oled.show()
        time.sleep(LOOPTIME)

def button_pressed():
    """Funkcja wywoływana po naciśnięciu przycisku"""
    capture()

button.when_pressed = button_pressed

oled_thread = Thread(target=oled_loop, daemon=True)
oled_thread.start()

pause()
