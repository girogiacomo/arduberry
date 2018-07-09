#include <avr/io.h>
#include <avr/wdt.h>

#define Reset_AVR() wdt_enable(WDTO_30MS); while(1) {} 

void setup() {
  for(int i=2; i<14; i++)
    pinMode(i, OUTPUT);

  Serial.begin(9600);
  Serial.setTimeout(100);
}

boolean decrementa, pulse;
boolean pin[14]={0,0,0,0,0,0,0,0,0,0,0,0,0,0};
String red, green, blue, duty;
String inString="";
char inChar;
int pmin=2, pmax=13, tmp=0, duty_pulse;

void Pulse(){
  analogWrite(3, 255-duty_pulse); 
  if(duty_pulse>254){decrementa = 1;}
  if(duty_pulse<10){decrementa = 0;}
  if (decrementa){duty_pulse--;}else{duty_pulse++;}
  delay(6);
}

void loop() {

if (pulse)
  Pulse();

if (Serial.available())
      delay(10);
      switch ((char) Serial.read()) { 
        case 'a':                                      // accensione
              inString = Serial.readStringUntil('*');
              tmp = inString.toInt();
              if (tmp>=pmin && tmp<=pmax){
                  digitalWrite(tmp, HIGH); 
                  pin[tmp]=true;
               }
          break;
        case 's':                                     // spegnimento
               inString = Serial.readStringUntil('*');
               tmp = inString.toInt();
               if (tmp>=pmin && tmp<=pmax){
                  digitalWrite(tmp, LOW); 
                  pin[tmp]=false;
               }
          break;
        case '?':                                     // stato
              for(int i=0; i<14; i++){
                if(pin[i]==true){
                  Serial.print(i);
                  Serial.print("|");
                }
              }
              Serial.print("r|");
              Serial.print(red);
              Serial.print("|g|");
              Serial.print(green);
              Serial.print("|b|");
              Serial.print(blue);
              Serial.print("|d|");
              Serial.print(duty);
              Serial.print("|");
              if(pulse)
                Serial.print("p|");       
          break;
       case 'R':
              Reset_AVR();
          break;
       case 'r':
              red = Serial.readStringUntil('*');
              analogWrite(9, red.toInt()); 
          break;
       case 'g':
              green = Serial.readStringUntil('*');
              analogWrite(10, green.toInt()); 
          break;
       case 'b':
              blue = Serial.readStringUntil('*');
              analogWrite(11, blue.toInt()); 
          break;
       case 'd':
              duty = Serial.readStringUntil('*');
              analogWrite(3, 255-(duty.toInt()));
          break;
       case 'p':
              inChar = (char)Serial.read();
              if (inChar == 'a'){pulse = HIGH; duty_pulse = duty.toInt();}
              if (inChar == 's'){pulse = LOW; analogWrite(3, 255-(duty.toInt()));}
          break;
        default:
          break;
        
      }
}

// r255b*g255*b255*d125*

/*                                                                // BOZZA PULSE
              while(1){
                analogWrite(3, 255-duty); 
                if(duty==255){decrementa = 1;}
                if(duty==1){decrementa = 0;}
                if (decrementa){duty--;}else{duty++;}
                delay(5);
              }                                             */

