
const express = require('express')
const router = express.Router()
const validator = require('validator');
const sanitize = require('sanitize-html');

const sanitizerConfig = {
  allowedTags: [],
  allowedAttributes: {},
};

router.get('/greeting', (req, res) => {
  const { name }  = req.query;
  if (!name || !validator.isAlphanumeric(name)) {
    return res.status(400).send('Name is required and must be alphanumeric');
  }
  const escapedName = sanitize(name, sanitizerConfig);
  res.send(`<h1> Hello : ${escapedName} </h1>`)
})

router.get('/greet-template', (req,res) => {
  const name = req.query.name;
  const csrfToken = req.query.csrfToken;
  if (!name || !validator.isAlphanumeric(name)) {
    return res.status(400).send('Name is required and must be alphanumeric');
  }
  if (!csrfToken || csrfToken !== req.csrfToken()) {
    return res.status(400).send('Invalid CSRF token');
  }
  const escapedName = sanitize(name, sanitizerConfig);
  res.render('index', { user_name: escapedName, csrfToken: req.csrfToken()});
})

router.post('/greet-template', (req,res) => {
  const name = req.body.name;
  const csrfToken = req.body.csrfToken;
  if (!name || !validator.isAlphanumeric(name)) {
    return res.status(400).send('Name is required and must be alphanumeric');
  }
  if (!csrfToken || csrfToken !== req.csrfToken()) {
    return res.status(400).send('Invalid CSRF token');
  }
  const escapedName = sanitize(name, sanitizerConfig);
  res.render('index', { user_name: escapedName, csrfToken: req.csrfToken()});
})

module.exports = router
