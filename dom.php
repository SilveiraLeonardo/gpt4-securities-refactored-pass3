
<?php
$name = trim(htmlentities(stripslashes($_GET['name'])));
$someObject = json_encode(array(
  "url" => "https://example.com/script.js",
  "integrity" => "sha384-...",
  "nonce" => base64_encode(openssl_random_pseudo_bytes(32)),
));
?>
<html>
  <head>
  </head>
  <body>
    <p>
      Hi, <?= htmlspecialchars(strip_tags($name)); ?>
    </p>
    <script>
      function sanitizeURL(url) {
        const allowedDomains = ["https://example.com", "https://example.org"];
        const a = document.createElement("a");
        a.href = url;
        if (allowedDomains.includes(a.origin)) {
          return a.href;
        } else {
          return null;
        }
      }

      window.onload = function () {
        let someObject = <?php echo $someObject; ?>;
        if (someObject.url && someObject.integrity && someObject.nonce) {
          let script = document.createElement("script");
          script.src = sanitizeURL(someObject.url);
          if (script.src) {
            script.integrity = someObject.integrity;
            script.crossOrigin = "anonymous";
            script.nonce = someObject.nonce;
            document.body.appendChild(script);
          }
        }
      };
    </script>
    <script nonce="<?php echo base64_encode(openssl_random_pseudo_bytes(32)); ?>" type="text/javascript">
      // JavaScript code
    </script>
  </body>
</html>
