
const express = require('express');
const config = require('../config');
const router = express.Router();

const MongoClient = require('mongodb').MongoClient;
const url = config.MONGODB_URI;
const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');
const validator = require('validator');
const rateLimit = require('express-rate-limit'); // Add express-rate-limiter library
const passwordValidator = require('password-validator'); // Add the password-validator library
const helmet = require('helmet'); // Add the helmet library

router.use(helmet()); // Use helmet middleware to set secure HTTP headers

const passwordSchema = new passwordValidator(); // Define a password schema using password-validator
passwordSchema
  .is().min(8)
  .has().uppercase()
  .has().lowercase()
  .has().digits()
  .has().not().spaces();

// Function to sanitize and validate input data
function sanitizeInput(input) {
  return validator.escape(input);
}

// Set up rate limiter for login endpoint
const loginLimiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutes
  max: 100,
  message: "Too many login attempts, please try again later."
});
router.use('/customers/login', loginLimiter);

router.post('/customers/register', async (req, res) => {
  // ... Rest of the code remains the same

  // Add password validation using passwordSchema
  if (!passwordSchema.validate(sanitizedData.password)) {
    return res.status(400).json({ status: "Error", message: "Weak password" });
  }

  // ... Rest of the code remains the same
});

router.post('/customers/find', async (req, res) => {
  //... Rest of the code remains the same
});

router.post('/customers/login', async (req, res) => {
  //... Rest of the code remains the same

  // Update error messages to be more generic
  if (!validator.isEmail(sanitizedData.email)) {
    return res.json({ status: "Error", "message": "Invalid credentials" });
  }
    //... Rest of the code remains the same
  if (result && bcrypt.compareSync(sanitizedData.password, result.password)) {
    //... Rest of the code remains the same
  } else {
    res.json({ status: "Error", message: "Invalid credentials" });
  }
  // ... Rest of the code remains the same
});

module.exports = router;
