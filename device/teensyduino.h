#include <TinyGPS++.h>

#define sim800 Serial2
#define gpsx Serial1


String lengthx, latx, lngx, timex, text_msg;
String token = ""; //put some token the same in devices table on server
int dev_id = 1;
volatile byte dev_mode = HIGH;
int mode0_start = 0, gps_done = 0, loopnmb = 0;
TinyGPSPlus gps;

void setup()  
{
  Serial.begin(9600);
  sim800.begin(9600);
  gpsx.begin(9600);
  delay(2000);
  
  pinMode(A1, INPUT);
  pinMode(A2, OUTPUT);
  attachInterrupt(A1, switchMode, CHANGE);

  digitalWrite(A2, HIGH);
  
  sim800.println("at\r\n");
  runsl();
  delay(1000);
  Serial.println("setup completed");
}


void loop() {
  
if (dev_mode == 1)

gps_read();

if (dev_mode == 0)
{
  
  if (mode0_start == 0)
  {
    Serial.println("konfigurowanie odbierania sms");
    delay(500);
    sim800.println("at+cmgf=1\r\n"); //sms text mode
    runsl();
    delay(500);
    sim800.println("at+cnmi=1,2,0,0,0\r\n"); //sms receive mdoe
    runsl();
    delay(500);
    mode0_start = 1;
    Serial.println("Pomyslnie skonfigurowanie odbieranie");
    delay(500);
    gps_done = 0;
  }
  
  else
  {
    if(sim800.available()>0)
    {
      text_msg = sim800.readString();
      Serial.println(text_msg);
      delay(10);
    }
    
    if(text_msg.toUpperCase().indexOf("ON")>=0) //start sending positions
    {
      digitalWrite(A2, HIGH);
      dev_mode = 1;
      text_msg = "";
    }
    
    if(text_msg.toUpperCase().indexOf("PULL")>=0)
    {
        digitalWrite(A2, HIGH);
        
        while (gps_done == 0)
        {
          gps_read();
        }
        digitalWrite(A2, LOW);
        gps_done = 0;
        text_msg = "";
    }
  }

}
}

void gps_read()
{  
    while (gpsx.available() > 0)
      if (gps.encode(gpsx.read()))
      {
        
         if (gps.location.isUpdated())
            {
              delay(2000);
              Serial.print("LAT="); Serial.print(gps.location.lat(), 6);
              Serial.print("LNG="); Serial.println(gps.location.lng(), 6);
              displayInfo();
            }
      
      }
  
    if (millis() > 5000 && gps.charsProcessed() < 10)
    {
      Serial.println(F("No GPS detected: check wiring."));
      delay(5000);
    }
}

void displayInfo()
{
  Serial.println(F("Location: ")); 
  if (gps.location.isValid())
  {
    Serial.println(String(gps.location.lng(), 6));
    Serial.println(String(gps.location.lat(), 6));
    Serial.println(String(gps.time.value()));
    
    if (mode0_start == 0)
    {
       sim_setup();
       mode0_start = 1;
    }
    sim800.println("at+sapbr=1,1\r\n");
    runsl();
    delay(3000);
    sim800.println("at+sapbr=2,1\r\n");
    runsl();
    delay(1000);
    sim800.println("at+httpinit\r\n");
    runsl();
    delay(1000);   
    sim800.println("at+httppara=\"CID\",1\r\n");
    runsl();
    delay(1000);
    sim800.println("at+httppara=\"URL\",\"<<IP>>/nittrack/dodaj.php\"\r\n"); //put your server address
    runsl();
    delay(1000);
    sim800.println("at+httppara=\"CONTENT\",\"application/x-www-form-urlencoded\"\r\n");
    runsl();
    delay(500);
    
      String urlData = "lng=";
      urlData += String(gps.location.lng(), 6);
      urlData += "&lat=";
      urlData += String(gps.location.lat(), 6);   
      urlData += "&speed=";
      urlData += String(gps.speed.kmph());
      urlData += "&gps_time=";
      urlData += String(gps.time.value());
      urlData += "&satellites=";
      urlData += String(gps.satellites.value());    
      urlData += "&course=";
      urlData += String(gps.course.deg());    
      urlData += "&dev_id=";
      urlData += String(dev_id);
      urlData += "&token=";
      urlData += String(token);
  
    //show data packet  
    Serial.println(urlData);
    lengthx = String(urlData.length());
    
    sim800.println("at+httpdata=" + lengthx + ",10000\r\n");
    delay(300);       
    sim800.println(urlData);
    delay(6700);
    sim800.println("at+httpaction=1\r\n");
    runsl();
    delay(3000);
    sim800.println("at+httpterm\r\n");
    runsl();
    delay(500);
    gps_done = 1;
      
  }
  else
  {
    Serial.println("narazie dupa");
    delay(5000);\
    return 0;
  }

}

void sim_setup()
{
    delay(200);
    sim800.println("at+sapbr=3,1,\"APN\",\"internet\"\r\n");
    runsl();
    delay(500);
    sim800.println("at+sapbr=3,1,\"USER\",\"internet\"\r\n");
    runsl();
    delay(500);
    sim800.println("at+sapbr=3,1,\"PWD\",\"internet\"\r\n");
    runsl();
    delay(500);
    Serial.println("Setup Complete!");
    delay(500);
}

void runsl() 
{
  while (sim800.available()) 
  {
    Serial.write(sim800.read());
  }
}

void switchMode()
{
  if (digitalRead(A1) == LOW)
  {
    dev_mode = LOW;
    digitalWrite(A2, LOW);
  }
  else
  {
    dev_mode = HIGH;
    digitalWrite(A2, HIGH); 
  }
  mode0_start = 0;
  
}