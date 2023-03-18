
const express = require('express');
const app = express();
const port = 3000;
const path = require('path');
const fs = require('fs');
const crypto = require('crypto');
const bodyParser = require('body-parser');
const session = require('express-session');
const helmet = require('helmet');
const rateLimit = require('express-rate-limit');
const validator = require('validator');

app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: false }));
app.use(
  session({
    secret: process.env.SESSION_SECRET || 'your-strong-secret-key',
    resave: false,
    saveUninitialized: true
  })
);
app.use(helmet());
const limiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max: 100
});
app.use(limiter);

app.get('/', async (req, res) => {
  if (req.session.user && req.session.user.isAuthenticated) {
    const fileName = path.basename(req.query.name);
    const filePath = path.join(__dirname, 'files', fileName);
    try {
      const exists = await fs.promises.access(filePath, fs.constants.F_OK);
      if (req.session.user.isAuthorized(fileName)) {
        const fileContent = await readFile(filePath, req.session.user);
        res.send(fileContent);
      } else {
        res.status(401).send('Unauthorized');
      }
    } catch (err) {
      if (err.code === 'ENOENT') {
        res.status(404).send('File not found');
      } else {
        res.status(500).send('Internal server error');
      }
    }
  } else {
    res.status(401).send('Unauthorized');
  }
});

async function readFile(path, user) {
  const fileName = path.split('/').pop();
  const sanitizedPath = validator.escape(path);
  if (fileName.match(/^[a-zA-Z0-9_\-]+$/) && (await fs.promises.access(sanitizedPath, fs.constants.F_OK))) {
    const fileContent = await fs.promises.readFile(sanitizedPath);
    const fileHash = crypto.createHash('sha256').update(fileContent).digest('hex');
    if (user.isAuthenticated() && user.isAuthorized(fileName) && user.isAuthorized(fileHash)) {
      return fileContent;
    } else {
      throw new Error('Unauthorized');
    }
  } else {
    throw new Error('Invalid file name');
  }
}

app.listen(port, () => {
  console.log(`Example app listening at http://localhost:${port}`);
});
