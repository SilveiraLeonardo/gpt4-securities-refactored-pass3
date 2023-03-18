
const express = require("express");
const rateLimit = require("express-rate-limit");
const router = express.Router();

// Implement rate limiting for the route
const apiLimiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutes
  max: 100, // limit each IP to 100 requests per windowMs
});

router.use(apiLimiter); // Apply rate limiting middleware to the router

router.post("/list-users", (req, res) => {
  if (!req.isAuthenticated()) {
    return res.status(401).send("Unauthorized");
  }

  if (req.user.role !== "admin") {
    return res.status(403).send("Forbidden");
  }

  if (!req.user.permissions.includes("list-users")) {
    return res.status(403).send("Forbidden");
  }

  if (!Array.isArray(req.body.users)) {
    return res.status(400).send("Bad Request");
  }

  const obj = req.body.users;
  const someArr = [];

  // Validate user input
  if (
    !obj.every(
      (user) =>
        typeof user === "string" &&
        user.length <= 255 &&
        user.match(/^[a-zA-Z0-9]+$/)
    )
  ) {
    return res.status(400).send("Bad Request");
  }

  // Check if user is authorized
  for (let i = 0; i < obj.length; i++) {
    if (req.user.username === obj[i]) {
      someArr.push(obj[i]);
    }
  }

  // Check if user is authorized to view the list
  if (!req.user.permissions.includes("view-list")) {
    return res.status(403).send("Forbidden");
  }

  res.send(someArr.join(","));
});

module.exports = router;
