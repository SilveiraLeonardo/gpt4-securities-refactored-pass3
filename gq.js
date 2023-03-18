
const { ApolloServer, gql } = require('apollo-server');
const fs = require('fs');
const express = require('express');
const path = require('path');
const sanitizePath = require('sanitize-filename');
const jwt = require('jsonwebtoken');
const SECRET_KEY = 'your_secret_key';
const app = express();
const AUTHORIZED_DIRECTORY = "/authorized/directory/";

const typeDefs = gql`
  type Book {
    title: String
    author: String
  }

  type Query {
    books(path: String): [Book]
  }
`;

const books = [
  {
    title: 'The Awakening',
    author: 'Kate Chopin',
  },
  {
    title: 'City of Glass',
    author: 'Paul Auster',
  },
];

const resolvers = {
  Query: {
    books: (parent, args, context, info) => {
      if (
        context.user &&
        context.user.hasPermission('read_file') &&
        context.user.isAuthenticated &&
        context.user.isAuthorized
      ) {
        // Validate the path
        const sanitizedPath = path.normalize(sanitizePath(args.path));
        const normalizedPath = path.join(AUTHORIZED_DIRECTORY, sanitizedPath);
        if (!normalizedPath.match(/^[a-zA-Z0-9/]+$/)) {
          throw new Error('Invalid path');
        }

        // Check if the file exists
        if (!fs.existsSync(normalizedPath)) {
          throw new Error('File not found');
        }

        const file = readFile(normalizedPath).toString();
        return [{ title: file, author: 'hello' }];
      } else {
        throw new Error('Unauthorized');
      }
    },
  },
};

function readFile(filePath) {
  return fs.readFileSync(filePath);
}

function getUserFromToken(token) {
  try {
    const decoded = jwt.verify(token, SECRET_KEY);
    return decoded;
  } catch (error) {
    return null;
  }
}

const server = new ApolloServer({
  introspection: process.env.NODE_ENV !== 'production',
  typeDefs,
  resolvers,
  context: ({ req }) => {
    const token = req.headers.authorization || '';
    const user = getUserFromToken(token);
    if (!user || !user.isAuthenticated || !user.isAuthorized) {
      throw new Error('Unauthorized');
    }
    return { user };
  },
  formatError: (error) => {
    // Your custom error format logic
    return error;
  },
});

const protectedPath = "/authorized/directory";

app.use(protectedPath, (req, res, next) => {
  const token = req.headers.authorization || '';
  const user = getUserFromToken(token);
  if (!user || !user.isAuthenticated || !user.isAuthorized) {
    res.status(403).send("Forbidden");
  } else {
    next();
  }
});

app.use(protectedPath, express.static(protectedPath));

server.listen().then(({ url }) => {
  console.log(`🚀 Server ready at ${url}`);
});
