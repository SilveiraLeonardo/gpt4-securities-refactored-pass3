
const jsyaml = require("js-yaml");
const sanitize = require('sanitize-html');
const { check, validationResult } = require('express-validator');
const express = require("express");
const bodyParser = require("body-parser");
const helmet = require("helmet"); // Add Helmet
const rateLimit = require("express-rate-limit"); // Add express-rate-limit
const cors = require("cors"); // Add CORS

const app = express();
app.use(bodyParser.json()); // Use this middleware to parse JSON request bodies.

app.use(helmet()); // Use Helmet middleware
app.use(rateLimit({ windowMs: 60 * 1000, max: 60 })); // Rate limit middleware: 60 requests per minute

const corsConfig = {
  origin: ['https://yourdomain.com'],
  optionsSuccessStatus: 200,
  methods: "GET,PUT,POST",
};

app.use(cors(corsConfig)); // CORS middleware with whitelist

app.post('/store/:id', [
  check('data').not().isEmpty().withMessage('Data is required')
], function (req, res) {
  // ... (rest of the code)
});

app.listen(3000, function () {
  console.log('Listening on port 3000...');
});
