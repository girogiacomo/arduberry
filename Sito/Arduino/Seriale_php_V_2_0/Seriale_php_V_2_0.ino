#include <avr/io.h>
#include <avr/wdt.h>
#include <SPI.h>
#include <RH_RF22.h>
#define Reset_AVR() wdt_enable(WDTO_30MS); while(1) {} 
RH_RF22 rf22;
int index=13;
int tmp=0, duty_pulse, pred=44, pgreen=45, pblue=46, pduty=4, pos=0;
#define pmin 22
#define pmax 43
void setup() {
  pinMode(pred, OUTPUT);
  pinMode(pgreen, OUTPUT);
  pinMode(pblue, OUTPUT);
  pinMode(pduty, OUTPUT);
  for(int i=pmin; i<=pmax; i++){
    pinMode(i, INPUT);
    i++;
    pinMode(i, OUTPUT);
  }
  Serial.begin(9600);
  Serial.setTimeout(100);
  pinMode(11, INPUT);
  pinMode(12, INPUT);
  pinMode(13, INPUT);
  if (!rf22.init())
    Serial.println("init failed");
  rf22.setModemConfig(index);
}

String key = "atxZ47CG";
boolean decrementa, pulse;
boolean pin[pmax+1]={0};
String red, green, blue, duty, sendstring;
String inString="";
char inChar;

void Pulse(){
  analogWrite(pduty, 255-duty_pulse); 
  if(duty_pulse>254){decrementa = 1;}
  if(duty_pulse<2){decrementa = 0;}
  if (decrementa){duty_pulse--;}else{duty_pulse++;}
  delay(6);
}

void loop() {

if (pulse)
  Pulse();

if (rf22.available()){
    uint8_t buf[RH_RF22_MAX_MESSAGE_LEN];
    uint8_t len = sizeof(buf);
    if (rf22.recv(buf, &len)){
      bool flag = HIGH;
      for(int pos = 0; pos < 8; pos++)
        if(key.charAt(pos) == char(buf[pos])){;}else{flag = LOW;}
      if(flag == HIGH){
      String inString = (char*)buf;
      Serial.println(inString);
      inString = inString.substring(8);
      Serial.println(inString);
      while(inString !=""){
          Serial.println(inString);
          char switchchar = inString.charAt(0);
          inString = inString.substring(1);
          switch (switchchar) { 
              case 'a':                                      // accensione
                    pos = inString.indexOf('*');
                    tmp = inString.substring(0, pos).toInt();
                    inString = inString.substring(pos);
                    Serial.println(tmp);
                    if (tmp>=pmin && tmp<=pmax){
                        digitalWrite(tmp, HIGH); 
                        pin[tmp]=true;
                     }
                break;
              case 's':                                     // spegnimento
                    pos = inString.indexOf('*');
                    tmp = inString.substring(0, pos).toInt();
                    inString = inString.substring(pos);
                    Serial.println(tmp);
                    if (tmp>=pmin && tmp<=pmax){
                        digitalWrite(tmp, LOW); 
                        pin[tmp]=false;
                    }
                break;
              case 't':                                     // toggle
                    pos = inString.indexOf('*');
                    tmp = inString.substring(0, pos).toInt();
                    inString = inString.substring(pos);
                    Serial.println(tmp);
                    if (tmp>=pmin && tmp<=pmax){
                        pin[tmp] = !pin[tmp];
                        digitalWrite(tmp, pin[tmp]); 
                     }
                break;
              case '?':                                     // stato
              {
                    sendstring = key;
                    for(int i=pmin; i<=pmax; i++){
                      if(pin[i]==true){
                        sendstring += i;
                        sendstring += "|";
                      }
                    }
                    sendstring += "r|";
                    sendstring += red;
                    sendstring += "|g|";
                    sendstring += green;
                    sendstring += "|b|";
                    sendstring += blue;
                    sendstring += "|d|";
                    sendstring += duty;
                    sendstring += "|";
                    if(pulse)
                      sendstring += "p|"; 
                    char data[sendstring.length()+1];
                    sendstring.toCharArray(data, sendstring.length()+1);
                    delay(150);
                    rf22.send(data, sizeof(data)); 
                    Serial.println((char*)data);
                    rf22.waitPacketSent(); 
              }
                break;
             case 'R':
                    Reset_AVR();
                break;
             case 'r':
                    pos = inString.indexOf('*');
                    red = inString.substring(0, pos);
                    inString = inString.substring(pos);
                    analogWrite(pred, red.toInt()); 
                break;
             case 'g':
                    pos = inString.indexOf('*');
                    green = inString.substring(0, pos);
                    inString = inString.substring(pos);
                    analogWrite(pgreen, green.toInt()); 
                break;
             case 'b':
                    pos = inString.indexOf('*');
                    blue = inString.substring(0, pos);
                    inString = inString.substring(pos);
                    analogWrite(pblue, blue.toInt());
                break;
             case 'd':
                    pos = inString.indexOf('*');
                    duty = inString.substring(0, pos);
                    inString = inString.substring(pos);
                    analogWrite(pduty, 255-(duty.toInt()));
                break;
             case 'p':
                    inChar = inString.charAt(0);
                    inString = inString.substring(1);
                    if (inChar == 'a'){pulse = HIGH; duty_pulse = duty.toInt();}
                    if (inChar == 's'){pulse = LOW; analogWrite(3, 255-(duty.toInt()));}
                break;
              default:
                break;
              
            }
        }
      }
    }
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

