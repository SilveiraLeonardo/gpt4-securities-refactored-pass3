
require('dotenv').config(); // Required to use environment variables
const mysql = require('mysql2/promise');
const https = require('https');

const options = { /* your request options */ };

const getValTom = () => {
  return new Promise((resolve, reject) => {
    https.request(options, (res) => {
      res.on('data', (chunk) => {
        resolve(chunk);
      });
      res.on('error', (error) => {
        reject(error);
      });
    }).end();
  });
};

const insertIntoDatabase = async (valTom) => {
  try {
    const connection = await mysql.createConnection({
      host: 'localhost',
      user: process.env.DB_USER,
      password: process.env.DB_PASSWORD,
      database: 'test',
    });

    const the_Query = "INSERT INTO Customers (CustomerName, ContactName) VALUES ('Tom', ?)";
    const [rows] = await connection.execute(the_Query, [valTom]);
    console.log('GENERATED id:', rows.insertId);

    connection.close();
  } catch (error) {
    console.log('Error:', error);
  }
};

getValTom()
  .then((valTom) => {
    if (valTom) {
      insertIntoDatabase(valTom);
    } else {
      console.log('Error: valTom is not set');
    }
  }).catch((error) => {
    console.log('Error:', error);
  });
