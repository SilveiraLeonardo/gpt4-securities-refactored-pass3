
const express = require('express');
const router = express.Router();
const validator = require('validator');
const session = require('express-session');

function isAuthenticated(req) {
  // Check the session for an authenticated user, e.g., req.session.user
  return req.session.user !== undefined;
}

// Add this middleware before defining routes
app.use(session({ /* Your session options here */ }));

router.get('/login', function (req, res) {
    let followPath = req.query.path;
    if (isAuthenticated(req)) {
        // Check if the path is relative
        if (followPath[0] !== '/') {
            followPath = '/' + followPath;
        }

        // Sanitize and encode the path
        let sanitizedPath = validator.escape(followPath);
        let encodedPath = encodeURI(sanitizedPath);
        let url = `https://example.com${encodedPath}`;
        res.redirect(url);
    } else {
        res.redirect('/');
    }
});

router.get('/goto', function (req, res) {
    let urlId = req.query.urlId;
    if (isAuthenticated(req)) {
        let sanitizedUrlId = validator.escape(urlId);
        // Fetch the link from your database or cache using the sanitizedUrlId
        let link = getUrlById(sanitizedUrlId);
        if (link) {
            res.redirect(link);
        } else {
            res.status(400).send('Invalid URL');
        }
    } else {
        res.redirect('/');
    }
});

module.exports = router;
