
const express = require('express');
const router = express.Router();
const jwt = require('jsonwebtoken');
const crypto = require('crypto');
const helmet = require('helmet');

router.use(helmet.contentSecurityPolicy({
  directives: {
    defaultSrc: ["'self'"],
    scriptSrc: ["'self'"],
    imgSrc: ["'self'"],
    styleSrc: ["'self'"],
    connectSrc: ["'self'"],
    fontSrc: ["'self'"],
  },
}));

const key = Buffer.alloc(32);
const iv = Buffer.alloc(12);

crypto.scrypt(process.env.JWT_SECRET, process.env.SALT, key.length, (err, derivedKey) => {
  if (err) throw err;
  key.copy(derivedKey);
});

crypto.randomFillSync(iv);

router.get("/tstMe/:id", (req, res) => {
    if (!req.headers.authorization) {
        return res.status(401).send('Authentication failed');
    }

    let token;
    try {
        token = jwt.verify(req.headers.authorization, process.env.JWT_SECRET);
    } catch (err) {
        return res.status(401).send('Authentication failed');
    }

    if (!token || token.role !== 'admin') {
        return res.status(401).send('Authentication failed');
    }

    if (!req.params.id || !req.params.id.match(/^[a-zA-Z0-9]+$/)) {
        return res.status(400).send('Invalid input');
    }

    if (token.id !== req.params.id) {
        return res.status(401).send('Authentication failed');
    }

    const cipher = crypto.createCipheriv('aes-256-gcm', key, iv);
    let encrypted = cipher.update(req.params.id, 'utf8', 'hex');
    encrypted += cipher.final('hex');

    const signature = crypto.createHmac('sha256', process.env.JWT_SECRET)
        .update(encrypted)
        .digest('hex');

    const nonce = crypto.randomBytes(16).toString('hex');
    const timestamp = Date.now();
    const hmac = crypto.createHmac('sha256', process.env.JWT_SECRET)
        .update(nonce + timestamp + encrypted)
        .digest('hex');

    const responseCipher = crypto.createCipheriv('aes-256-gcm', key, iv);
    let responseEncrypted = responseCipher.update(JSON.stringify({ encrypted, signature, nonce, timestamp, hmac }), 'utf8', 'hex');
    responseEncrypted += responseCipher.final('hex');

    res.send(responseEncrypted);
});

module.exports = router;
