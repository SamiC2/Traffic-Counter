# Import required libraries
import MySQLdb
import pyfirmata
from time import sleep
import time
import calendar


# ------------------------------------------------------
#  Name: DB
# PreCondition: the server name, username, password, and name of the database
# PostCondition: represents connection to DB and related functions
# ------------------------------------------------------
class DB:
    # ------------------------------------------------------
    # Name: DB contructor
    # PreCondition: the server name, username, password, and name of the database
    # Description: connects to MYSQL database
    # ------------------------------------------------------
    def __init__(self, hostName, userName, password, dbName):
        try:
            self.db = MySQLdb.connect(host=hostName,  # your host
                                      user=userName,  # username
                                      password=password,  # password
                                      db=dbName)  # name of the database
        except MySQLdb.Error:
            print("ERROR: Connection TO Database FAULTY!")
        self.cur = self.db.cursor()

    # ------------------------------------------------------
    # Name: add
    # PreCondition: Nothing
    # PostCondition: adds the current time as an entry to the MYSQL database
    # ------------------------------------------------------
    def add(self, location, entering):
        # Inserting Into DB
        query = "INSERT INTO dbname.Traffic (location,entering) VALUES ('" \
                + location + "','" + entering + "')"
        self.cur.execute(query)

        # Make sure data is committed to the database, then close
        self.db.commit()


# ------------------------------------------------------
# Name: Arduino
# PreCondition: the name of the port the Arduino is connected to
# Description: represents connection to Arduino and related functions
# ------------------------------------------------------
class Arduino:
    # ------------------------------------------------------
    # Name: Arduino contructor
    # PreCondition: the name of the port the Arduino is connected to
    # postCondition: connects to Arduino
    # ------------------------------------------------------
    def __init__(self, port):
        # Associate port and board with pyFirmata
        self.board = pyfirmata.Arduino(port)
        # Use iterator thread to avoid buffer overflow
        self.it = pyfirmata.util.Iterator(self.board)
        self.it.start()

    # ------------------------------------------------------
    # Name: countPeople
    # PreCondition: the server name, username, password, and name of the database
    # postCondition: connects to the database and then adds an entry if it detects a guest
    # ------------------------------------------------------
    def countPeople(self, host, user, password, db, lot):
        self.db = DB(host, user, password, db)
        triggered = False
        # Define pins
        pirPin1 = self.board.get_pin('d:7:i')
        pirPin2 = self.board.get_pin('d:6:i')

        # Check for PIR sensor input
        while True:
            # Ignore case when receiving None value from pin

            while (pirPin1.read() is None or pirPin2.read() is None):
                pass
            if (pirPin1.read() is True and triggered is False):
                triggered = True
                if pirPin2.read() is not True:
                    print("Adding to DB")
                    self.db.add(lot,1)
                else:
                    print("Not adding")

            elif (pirPin2.read() is True and triggered is False):
                triggered = True
                if pirPin1.read() is not True:
                    print("Adding to DB")
                    self.db.add(lot,0)
                else:
                    print("Not adding")
            else:
                triggered = False
        # Release the board
        board.exit()

    def writeLED(self, pin, status):
        self.board.digital[pin].write(status)


def main():
    ard = Arduino("COM4")
    ard.countPeople("database.cse.tamu.edu", "justinlovelace", "test", "justinlovelace","lotXX")


main()
