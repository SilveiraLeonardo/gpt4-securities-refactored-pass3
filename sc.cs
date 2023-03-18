
using System;
using System.Security.Cryptography;
using System.Text;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Configuration;

namespace WebFox.Controllers
{
    [Route("secureCookieTest")]
    public class SecureCookieTest1 : ControllerBase
    {
        private readonly IConfiguration _configuration;

        public SecureCookieTest1(IConfiguration configuration)
        {
            _configuration = configuration;
        }

        [HttpGet("DoAction")]
        public IActionResult DoAction()
        {
            string password = "p-" + RandomNumberGenerator.GetInt32(200000000, 2000000000);
            string encryptedPassword = EncryptString(password);

            CookieOptions options = new CookieOptions
            {
                Path = "/",
                Domain = "",
                HttpOnly = true,
                Secure = true,
                SameSite = SameSiteMode.Strict
            };
            Response.Cookies.Append("password", encryptedPassword, options);
            return Ok();
        }

        private string EncryptString(string plainText)
        {
            byte[] plainTextBytes = Encoding.UTF8.GetBytes(plainText);

            // Get these values from a key vault, environment variables, or a configuration management system.
            string PasswordHash = _configuration["SecurePasswordHash"];
            string SaltKey = _configuration["SecureSaltKey"];
            byte[] keyBytes = new Rfc2898DeriveBytes(PasswordHash, Encoding.UTF8.GetBytes(SaltKey)).GetBytes(256 / 8);

            using (var symmetricKey = new AesGcm(keyBytes))
            {
                byte[] nonce = new byte[AesGcm.NonceByteSizes.MaxSize];
                RandomNumberGenerator.Fill(nonce);

                byte[] cipherTextBytes = new byte[plainTextBytes.Length];
                byte[] tag = new byte[AesGcm.TagByteSizes.MaxSize];

                symmetricKey.Encrypt(nonce, plainTextBytes, cipherTextBytes, tag);

                var result = new byte[nonce.Length + cipherTextBytes.Length + tag.Length];
                Buffer.BlockCopy(nonce, 0, result, 0, nonce.Length);
                Buffer.BlockCopy(cipherTextBytes, 0, result, nonce.Length, cipherTextBytes.Length);
                Buffer.BlockCopy(tag, 0, result, nonce.Length + cipherTextBytes.Length, tag.Length);

                return Convert.ToBase64String(result);
            }
        }
    }
}
