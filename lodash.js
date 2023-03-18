
const express = require('express');
const router = express.Router()

const _ = require('lodash');
const jwt = require('jsonwebtoken');
const crypto = require('crypto');

function check(req, res) {

  if (!req.body.config) {
    return res.status(400).send('Invalid request');
  }

  let config = {};
  try {
    config = JSON.parse(req.body.config);
  } catch (err) {
    console.log(err);
    return res.status(400).send('Invalid request');
  }

  const user = getCurrentUser(req);
  if (!user) {
    return res.status(401).send('Unauthorized');
  }

  const tokenValid = validateToken(req.headers.authorization, user);

  if (validateUser(user) && _.isMatch(config, {'allowed': true}) && validateInput(config) && tokenValid) {
    res.send('Welcome Admin');
  } else {
    res.send('Welcome User');
  }
}

function getCurrentUser(req) {
  if (req.session && req.session.user) {
    const iv = new Buffer.from(req.session.iv, 'hex'); // Get stored IV
    const decipher = crypto.createDecipheriv('aes-256-gcm', process.env.SESSION_SECRET, iv);
    let decrypted = decipher.update(req.session.user, 'hex', 'utf8');
    decrypted += decipher.final('utf8');
    return JSON.parse(decrypted);
  }
  return false;
}

function validateUser(user) {
  return user.isAdmin && user.isAuthenticated;
}

function validateInput(config) {
  // Perform more extensive validation here
  // check for allowed properties and malicious values
  return (config.allowed === true && typeof config.allowed === 'boolean');
}

function validateToken(token, user) {
  if (!process.env.JWT_SECRET) {
    console.log('Ensure JWT_SECRET environment variable is set correctly');
    return false;
  }
  // Validate the token here
  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET);
    return (validateUser(decoded) && decoded.id === user.id); // Check if user ID is also matching
  } catch (err) {
    console.log(err);
  }
  return false;
}

router.post('/check-user', check)

module.exports = router
