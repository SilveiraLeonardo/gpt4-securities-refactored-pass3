
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.Security.Application;
using Microsoft.AspNetCore.WebUtilities;

namespace WebFox.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class XSS : ControllerBase
    {
        [HttpGet] // Add an attribute to specify the method's behavior
        public async Task<IActionResult> Xss(string userInfo)
        {
            var context = ControllerContext.HttpContext;

            // Validate user input
            if (string.IsNullOrWhiteSpace(userInfo))
            {
                return BadRequest("Invalid user input."); // Return a response indicating an error
            }

            // Encode user input
            var encodedUserInfo = System.Net.WebUtility.HtmlEncode(userInfo);
            var sanitizedUserInfo = Sanitizer.GetSafeHtmlFragment(encodedUserInfo);

            // Encode output
            var encodedOutput = sanitizedUserInfo;

            // Set response headers
            context.Response.Headers.Add("X-XSS-Protection", "1; mode=block");
            context.Response.Headers.Add("Content-Security-Policy", "default-src 'self'");
            context.Response.Headers.Add("X-Content-Type-Options", "nosniff");
            context.Response.Headers.Add("X-Frame-Options", "deny");

            return Content("<body>" + encodedOutput + "</body>", "text/html"); // Return a ContentResult with the encoded output wrapped in <body> tags and a content type
        }
    }
}
