
const express = require('express');
const router = express.Router()
const { execFile } = require('child_process');
const Joi = require('joi');
const path = require('path');
const validator = require('validator');

const whitelist = ['ping', 'gzip'];

router.post('/ping', (req, res) => {
  const schema = Joi.object({
    url: Joi.string().custom((value, helpers) => {
      if (!whitelist.includes(value) || !validator.isURL(value)) {
        return helpers.error('Invalid URL');
      }
      return value;
    }).required()
  });

  const { error, value } = schema.validate(req.body);
  if (error) {
    return res.status(400).send('Invalid input');
  }

  execFile(value.url, [], { timeout: 1000 }, (error) => {
    if (error) {
      return res.send('error');
    }
    res.send('pong');
  })
});

router.post('/gzip', (req, res) => {
  const schema = Joi.object({
    file_path: Joi.string()
    .custom((value, helpers) => {
      const newPath = path.normalize(value);
      if (value !== newPath) {
        return helpers.error('Invalid file path');
      }
      return newPath;
    }).required()
  });

  const { error, value } = schema.validate(req.query);
  if (error) {
    return res.status(400).send('Invalid input');
  }

  execFile('gzip', [value.file_path], { timeout: 1000 }, (error) => {
    if (error) {
      return res.send('error');
    }
    res.send('done');
  });
});

router.get('/run', (req, res) => {
  const schema = Joi.object({
    cmd: Joi.string().valid(...whitelist).required(),
    args: Joi.array().items(Joi.string().allow(''))
  });

  const { error, value } = schema.validate(req.params);
  if (error) {
    return res.status(400).send('Invalid input');
  }

  execFile(value.cmd, value.args || [], { timeout: 1000 }, (error) => {
    if (error) {
      return res.send('error');
    }
    res.send('done');
  })
});

module.exports = router;
