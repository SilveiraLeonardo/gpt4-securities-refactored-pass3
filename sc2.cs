
using System;
using System.Security.Cryptography;
using System.Text;
using Microsoft.AspNetCore.Http;

namespace WebFox.Controllers
{
    public class SecureCookieTest2
    {
        public void ProcessRequest(HttpContext context)
        {
            SaveSecureCookie(context);
        }

        public void SaveSecureCookie(HttpContext context)
        {
            using var generator = new RNGCryptoServiceProvider();
            var passwordBytes = new byte[32];
            generator.GetBytes(passwordBytes);
            string password = Convert.ToBase64String(passwordBytes);

            byte[] data = Encoding.UTF8.GetBytes(password);
            byte[] encryptedData = ProtectedData.Protect(data, null, DataProtectionScope.CurrentUser);
            string encryptedPassword = Convert.ToBase64String(encryptedData);

            var passwordCookie = new CookieOptions
            {
                HttpOnly = true,
                Secure = true,
                SameSite = SameSiteMode.Strict
            };
            context.Response.Cookies.Append("encryptedPass", encryptedPassword, passwordCookie);
        }
    }
}
