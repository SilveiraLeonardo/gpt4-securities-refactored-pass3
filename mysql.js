
const express = require('express');
const router = express.Router();
const { check, validationResult } = require('express-validator');
const rateLimit = require('express-rate-limit');
const helmet = require('helmet');

const config = require('../../config');
const mysql = require('mysql');
const connection = mysql.createConnection({
  host: config.MYSQL_HOST,
  port: config.MYSQL_PORT,
  user: config.MYSQL_USER,
  password: config.MYSQL_PASSWORD,
  database: config.MYSQL_DB_NAME,
});

connection.connect();

router.use(helmet());

const apiLimiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutes
  max: 100, // limit each IP to 100 requests per windowMs
});

// Apply rate limiting to routes
router.use('/example1/', apiLimiter);
router.use('/example2/', apiLimiter);
router.use('/example3/', apiLimiter);

router.get('/example1/user/:id', [check('id').isNumeric()], (req, res) => {
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    return res.status(400).json({ errors: errors.array() });
  }

  let userId = req.params.id;
  let query = {
    sql: 'SELECT * FROM users WHERE id=?',
    values: [userId],
  };
  connection.query(query, (err, result) => {
    if (err) {
      res.status(400).json({
        error: err,
      });
    } else {
      res.json(result);
    }
  });
});

router.get('/example2/user/:id', [check('id').isNumeric()], (req, res) => {
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    return res.status(400).json({ errors: errors.array() });
  }

  let userId = req.params.id;
  let query = {
    sql: 'SELECT * FROM users WHERE id=?',
    values: [userId],
  };
  connection.query(query, (err, result) => {
    if (err) {
      res.status(400).json({
        error: err,
      });
    } else {
      res.json(result);
    }
  });
});

router.get('/example3/user/:id', [check('id').isNumeric()], (req, res) => {
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    return res.status(400).json({ errors: errors.array() });
  }

  let userId = req.params.id;
  let query = {
    sql: 'SELECT * FROM users WHERE id=?',
    values: [userId],
  };
  connection.query(query, (err, result) => {
    if (err) {
      res.status(400).json({
        error: err,
      });
    } else {
      res.json(result);
    }
  });
});

module.exports = router;
