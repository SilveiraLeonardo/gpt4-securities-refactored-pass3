
var express = require('express')
var app = express()
const cors = require('cors')
const bodyParser = require('body-parser');
const bcrypt = require('bcrypt');
const Sequelize = require('sequelize');
const sequelize = new Sequelize('database', 'username', 'password', {
  dialect: 'sqlite',
  storage: 'data/juiceshop.sqlite'
});

// Define allowed origins
const whitelistedOrigins = ['http://localhost:3000', 'https://example.com'];

const corsOptions = {
  origin: function (origin, callback) {
    if (whitelistedOrigins.indexOf(origin) !== -1) {
      callback(null, true);
    } else {
      callback(new Error('Not allowed by CORS'))
    }
  }
};

app.use(cors(corsOptions));
app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());

app.post('/login', function (req, res) {
    const username = req.body.username;
    const password = req.body.password;

    // Validate user input
    if (!username || !password || username.length > 50 || password.length > 50) {
        return res.status(400).send('Username and password must be less than 50 characters.');
    }

    // Query the database to get the user's hashed password using a prepared statement
    const query = 'SELECT * FROM Users WHERE username = ?';
    sequelize.query(query, {
        replacements: [username],
        type: sequelize.QueryTypes.SELECT
    })
      .then(users => {
        if (users.length === 0) {
          return res.status(401).send('Invalid username.');
        }

        const user = users[0];
        const isPasswordValid = bcrypt.compareSync(password, user.password);

        if (isPasswordValid) {
          // Authentication successful; handle sending access token or returning user object
          return res.status(200).json({ success: true, message: 'Authentication successful.', user });
        } else {
          // Authentication failed; return an error message
          return res.status(401).send('Invalid password.');
        }
      })
      .catch(err => {
        // Error handling
        res.status(500).send('An error occurred while processing the request.');
      });
});
