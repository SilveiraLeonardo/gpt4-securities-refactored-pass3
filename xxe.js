
const express = require('express');
const libxmljs2 = require('libxmljs2');
const db = require('db');
const router = express.Router();

// Define a maximum allowed upload size here
const MAX_UPLOAD_SIZE = 5 * 1024 * 1024; // 5MB

router.post('/upload-products', async (req, res) => {
  if (!req.user) {
    return res.status(401).send('Unauthorized');
  }

  if (!req.files || !req.files.products || req.files.products.data.length > MAX_UPLOAD_SIZE) {
    return res.status(400).send('Invalid file or file size too large');
  }

  const XMLfile = req.files.products.data;

  let products;
  try {
    products = libxmljs2.parseXmlString(XMLfile, { noent: false, noblanks: true });
  } catch (err) {
    return res.status(400).send('Invalid XML format');
  }

  const productNodes = products.root().childNodes();

  try {
    const productPromises = productNodes.map(async (product) => {
      const childNodes = product.childNodes();
      const nameNode = childNodes.find((node) => node.name() === 'name');
      const descriptionNode = childNodes.find((node) => node.name() === 'description');

      if (!nameNode || !descriptionNode) {
        throw new Error('Invalid product data');
      }

      let newProduct = new db.Product();
      newProduct.name = nameNode.text();
      newProduct.description = descriptionNode.text();
      await newProduct.save();
    });

    await Promise.all(productPromises);

    res.send('Thanks');
  } catch (err) {
    res.status(400).send(err.message);
  }
});

module.exports = router;
