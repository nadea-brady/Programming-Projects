-- Table: Users
CREATE TABLE Users (
    User_Id int NOT NULL AUTO_INCREMENT,
    User_Type varchar(20) NOT NULL,
    User_Name varchar(50) NOT NULL,
    User_Password varchar(255) NOT NULL,
    User_Email varchar(50) NOT NULL,
    CONSTRAINT Users_pk PRIMARY KEY (User_Id)
);

-- Table: Course
CREATE TABLE Course (
    Course_ID int NOT NULL AUTO_INCREMENT,
    Course_Name varchar(50) NOT NULL,
    Course_Section varchar(50) NOT NULL,
    CONSTRAINT Course_pk PRIMARY KEY (Course_ID)
);

-- Table: Notes
CREATE TABLE Notes (
    Note_ID int NOT NULL AUTO_INCREMENT,
    Note_Size int NOT NULL,
    Note_Date date NOT NULL,
    Users_User_Id int NOT NULL,
    Course_Course_ID int NOT NULL,
    Note_Path varchar(255) NOT NULL,
    Note_Name varchar(255) NOT NULL,
    CONSTRAINT Notes_pk PRIMARY KEY (Note_ID),
    CONSTRAINT Notes_Course FOREIGN KEY (Course_Course_ID) REFERENCES Course (Course_ID),
    CONSTRAINT Notes_Users FOREIGN KEY (Users_User_Id) REFERENCES Users (User_Id)
);

-- Table: UsersCourse
CREATE TABLE UsersCourse (
    UserCourse_Id int NOT NULL AUTO_INCREMENT,
    Course_Course_ID int NOT NULL,
    Users_User_Id int NOT NULL,
    CONSTRAINT UsersCourse_pk PRIMARY KEY (UserCourse_Id),
    CONSTRAINT UsersCourse_Course FOREIGN KEY (Course_Course_ID) REFERENCES Course (Course_ID),
    CONSTRAINT UsersCourse_Users FOREIGN KEY (Users_User_Id) REFERENCES Users (User_Id)
);