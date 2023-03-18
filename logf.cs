
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Logging;
using Microsoft.Security.Application;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace WebFox.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class LogInjection : ControllerBase
    {
        private readonly ILogger<LogInjection> _logger;

        public LogInjection(ILogger<LogInjection> logger)
        {
            _logger = logger;
        }

        public class UserInfoRequest
        {
            public string UserInfo { get; set; }
        }

        [HttpPost]
        [Authorize] // Ensure proper authentication middleware setup
        public void LogUserActivity([FromBody] UserInfoRequest userInfoRequest)
        {
            // Validate user input
            if (string.IsNullOrWhiteSpace(userInfoRequest.UserInfo))
            {
                _logger.LogError("error!! Invalid user input");
            }
            else
            {
                // Sanitize user input using Microsoft's AntiXSS library
                string sanitizedUserInfo = Encoder.HtmlEncode(userInfoRequest.UserInfo);

                _logger.LogError("error!! " + sanitizedUserInfo);
            }
        }
    }
}
