
import depthLimit from 'graphql-depth-limit'
import express from 'express'
import graphqlHTTP from 'express-graphql'
import schema from './schema'
import jwt from 'jsonwebtoken'
import rateLimit from 'express-rate-limit'
import cors from 'cors'
import helmet from 'helmet'

const app = express()
app.use(cors()) // Add CORS middleware
app.use(helmet()) // Add Helmet middleware

// depthlimit prevents nested queries
app.use('/graphql', rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutes
  max: 100 // limit each IP to 100 requests per windowMs
}), (req, res, next) => {
  const token = req.headers.authorization
  let user = null
  if (token) {
    try {
      user = jwt.verify(token, process.env.JWT_SECRET)
    } catch (err) {
      return next(err) // Pass the error to the next middleware
    }
  }
  if (!user) {
    return res.status(401).json({
      message: 'Unauthorized'
    })
  }

  // Validate user input using a validation library or custom validation logic
  const query = req.body.query
  if (query && query.length > 1000) {
    return res.status(400).json({
      message: 'Query too long'
    })
  }

  // Perform additional input validation here as necessary

  graphqlHTTP({
    schema,
    validationRules: [depthLimit(10)],
    context: { user }
  })(req, res, next)
})

// Add custom error handling middleware
app.use((err, req, res, next) => {
  res.status(err.status || 500)
  res.json({ message: err.message })
})

export default app
