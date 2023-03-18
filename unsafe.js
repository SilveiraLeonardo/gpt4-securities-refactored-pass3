
const express = require("express");
const cookieParser = require("cookie-parser");
const escape = require("escape-html");
const crypto = require("crypto");
const dotenv = require("dotenv");

dotenv.config();
const app = express();
app.use(cookieParser());

app.get("/", function (req, res) {
  if (req.cookies.profile) {
    const iv = req.cookies.iv;
    const authTag = req.cookies.authtag; // Retrieve the authTag from the cookies
    if (!iv || !authTag) {
      res.status(500).send("Decryption failed due to missing IV or AuthTag.");
      return;
    }
    try {
      let decipher = crypto.createDecipheriv(
        "aes-256-gcm",
        Buffer.from(process.env.SECRET_KEY, "base64"),
        Buffer.from(iv, "base64")
      );
      decipher.setAuthTag(Buffer.from(authTag, "base64")); // Decryptor should use the authTag
      var decrypted = decipher.update(req.cookies.profile, "base64", "utf8"); 
      decrypted += decipher.final("utf8");
     
      var obj = JSON.parse(decrypted);
      if (obj.username) {
        res.send("Hello " + escape(obj.username));
        return;
      }
    } catch (error) {
      console.log("Error: Decryption failed. " + error.message);
      res.status(500).send("Decryption failed.");
      return;
    }
  } else {
    const iv = crypto.randomBytes(12);
    let cipher = crypto.createCipheriv(
      "aes-256-gcm",
      Buffer.from(process.env.SECRET_KEY, "base64"),
      iv
    );
    let encrypted = cipher.update(
      JSON.stringify({
        username: "aqin",
        country: "india",
        city: "bangalore",
      }),
      "utf8",
      "base64"
    );
    encrypted += cipher.final("base64");
    const authTag = cipher.getAuthTag().toString("base64"); // Store the authTag after encryption
    res.cookie("profile", encrypted, {
      maxAge: 900000,
      httpOnly: true,
      secure: true,
      sameSite: "Strict",
      domain: "example.com",
    });
    res.cookie("iv", iv.toString("base64"), {
      maxAge: 900000,
      secure: true,
      sameSite: "Strict",
    });
    res.cookie("authtag", authTag, { // Store the authTag in a separate cookie
      maxAge: 900000,
      secure: true,
      sameSite: "Strict",
    });
  }
  res.send("Hello World");
});
app.listen(3000);
