
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using System;
using System.DirectoryServices.AccountManagement;
using System.Text.RegularExpressions;

namespace WebFox.Controllers
{
    [Authorize]
    [Route("api/[controller]")]
    [ApiController]
    public class LDAP : ControllerBase
    {
        public class UserInput
        {
            public string User { get; set; }
        }

        [HttpPost]
        public IActionResult LdapInje([FromBody] UserInput input)
        {
            string user = input.User;

            // Validate user input
            if (!Regex.IsMatch(user, @"^[a-zA-Z0-9_]+$"))
            {
                throw new ArgumentException("Invalid user name");
            }

            // Escape user input
            user = System.Web.Security.AntiXss.AntiXssEncoder.HtmlEncode(user, false);

            // Use System.DirectoryServices.AccountManagement for user lookup
            using (var context = new PrincipalContext(ContextType.Domain, "mycompany.com"))
            {
                UserPrincipal userPrincipal = UserPrincipal.FindByIdentity(context, IdentityType.Name, user);

                if (userPrincipal != null)
                {
                    return Ok("User found");
                }
                else
                {
                    return NotFound("User not found");
                }
            }
        }
    }
}
