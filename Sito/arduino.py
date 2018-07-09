import serial
arduino=serial.Serial('/dev/ttyACM0')
#arduino=serial.Serial('/dev/ttyUSB0')
print arduino
# while 1!=0:
#	valore = raw_input('Inserisci una stringa:')
#arduino.write('?')
#print (arduino.read())


