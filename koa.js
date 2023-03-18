
const Koa = require('koa');
const { URL } = require('url');
const { query } = require('koa-query-validator');
const { escape } = require('sanitize-html');
const app = new Koa();

const allowedDomains = ['example1.com', 'example2.com'];

app.use(
	query({
		target: {
			type: 'string',
			scale: 1,
			required: true,
			format: /(https?|ftp):\/\/[^\s/$.?#].[^\s]*/,
		},
	})
);

app.use(async (ctx) => {
	try {
		const parsedURL = new URL(ctx.query.target);
		if (!allowedDomains.includes(parsedURL.hostname)) {
			ctx.throw(400, 'URL Not Allowed');
		} else {
			const sanitizedURL = escape(parsedURL.toString());
			ctx.redirect(sanitizedURL);
		}
	} catch (error) {
		ctx.throw(400, 'Invalid URL');
	}
});

app.listen(3000);
