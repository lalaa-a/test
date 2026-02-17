CREATE TABLE
  transactions(
 		trasactionID INTEGER PRIMARY KEY AUTO_INCREMENT,
      	userID INTEGER UNIQUE ,
      	amount FLOAT ,
      	type TEXT ,
      	transactionDate DATE DEFAULT CURRENT_DATE,
      	transactionTime TIME DEFAULT CURRENT_TIME,
      	transaction_status ENUM (
            "Pending",
            "Completed",
            "Cancellation Request"),
      	actions ENUM (
            "View Details" ,
            "Process" ,
            "Process Refund" ) ,
      FOREIGN KEY (userID) REFERENCES users (id) ON DELETE CASCADE,
      INDEX (userID)
  
);