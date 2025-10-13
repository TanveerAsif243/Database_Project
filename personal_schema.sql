-- Department table
CREATE TABLE Department (
    DeptID INT PRIMARY KEY ,
    Dname VARCHAR(100) NOT NULL,
    Doffice VARCHAR(50),
    Dphone VARCHAR(20),
    Dcode VARCHAR(10) UNIQUE
);

-- Student table
CREATE TABLE Student (
    Sid INT PRIMARY KEY ,
    Sname VARCHAR(100) NOT NULL,
    Address VARCHAR(255),
    Phone VARCHAR(20),
    DeptID INT,
    FOREIGN KEY (DeptID) REFERENCES Department(DeptID)
);

-- Alumni table
CREATE TABLE Alumni (
    AlumniID VARCHAR(100) PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Email VARCHAR(100),
    Position VARCHAR(100),
    Company VARCHAR(100),
    Graduation_Year VARCHAR(20),
    DeptID INT,
    Degree VARCHAR(100),
    FOREIGN KEY (DeptID) REFERENCES Department(DeptID)
);

-- Degree table
CREATE TABLE Degree (
    Email VARCHAR(100) PRIMARY KEY,
    DegreeName VARCHAR(100) NOT NULL,
    Sid INT,
    FOREIGN KEY (Sid) REFERENCES Student(Sid)
);

-- Instructors table
CREATE TABLE instructors (
    INS_ID VARCHAR(10) PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Rank VARCHAR(50),
    Class VARCHAR(120),
    DeptID INT,
    FOREIGN KEY (DeptID) REFERENCES Department(DeptID)
);

-- Courses table
CREATE TABLE courses (
    Ccode VARCHAR(100) PRIMARY KEY,
    Cname VARCHAR(100) NOT NULL,
    DeptID INT,
    FOREIGN KEY (DeptID) REFERENCES Department(DeptID)
);

-- Section table
CREATE TABLE Section (
    secNo INT PRIMARY KEY ,
    Semester VARCHAR(50),
    Ccode VARCHAR(100),
    INS_ID VARCHAR(10),
    FOREIGN KEY (INS_ID) REFERENCES instructors(INS_ID),
    FOREIGN KEY (Ccode) REFERENCES courses(Ccode)
);

-- Classroom table
CREATE TABLE Classroom (
    RoomN VARCHAR(10),
    BLDG VARCHAR(50),
    secNo INT,
    PRIMARY KEY (RoomN, secNo),
    FOREIGN KEY (secNo) REFERENCES Section(secNo)
);

-- Takes table
CREATE TABLE Takes (
    Sid INT NOT NULL,
    Ccode VARCHAR(100),
    secNo INT NOT NULL,
    PRIMARY KEY (Sid, secNo), -- composite key added
    FOREIGN KEY (Sid) REFERENCES Student(Sid),
    FOREIGN KEY (secNo) REFERENCES Section(secNo),
    FOREIGN KEY (Ccode) REFERENCES courses(Ccode)
);

-- Assigned table
CREATE TABLE Assigned (
    INS_ID VARCHAR(10),
    secNo INT,
    PRIMARY KEY (INS_ID, secNo),
    FOREIGN KEY (INS_ID) REFERENCES instructors(INS_ID),
    FOREIGN KEY (secNo) REFERENCES Section(secNo)
);

-- RoutineTime table
CREATE TABLE RoutineTime (
    RoutineID INT PRIMARY KEY AUTO_INCREMENT,
    Sid INT NOT NULL,
    secNo INT NOT NULL,
    ClassTime VARCHAR(120),
    Day VARCHAR(15) NOT NULL,
    FOREIGN KEY (Sid, secNo) REFERENCES Takes(Sid, secNo)
    -- removed bad FK on ClassTime
);

-- Priority table
CREATE TABLE Priority (
    PriorityID INT PRIMARY KEY AUTO_INCREMENT;,
    Sid INT NOT NULL,
    Ccode VARCHAR(100) NOT NULL,
    INS_ID VARCHAR(10) NOT NULL,
    FOREIGN KEY (Sid) REFERENCES Student(Sid),
    FOREIGN KEY (Ccode) REFERENCES courses(Ccode),
    FOREIGN KEY (INS_ID) REFERENCES instructors(INS_ID)
);
