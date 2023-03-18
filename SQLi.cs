
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Logging;
using System;
using System.ComponentModel.DataAnnotations;
using System.Data.SqlClient;
using System.Text;
using System.Threading.Tasks;

namespace WebFox.Controllers
{
    [ApiController]
    [Route("[controller]")]
    public class Sqli : ControllerBase
    {
        private readonly ILogger<Sqli> _logger;
        private readonly string _connectionString;

        public Sqli(ILogger<Sqli> logger, string connectionString)
        {
            _logger = logger;
            _connectionString = connectionString;
        }

        [HttpGet("{id}")]
        public async Task<ActionResult<string>> DoSqliAsync([Required] string id)
        {
            if (!ModelState.IsValid)
            {
                return BadRequest(ModelState);
            }

            try
            {
                using (SqlCommand cmd = new SqlCommand("SELECT userName FROM users WHERE userId = @id"))
                {
                    using (SqlConnection con = new SqlConnection(_connectionString))
                    {
                        await con.OpenAsync();
                        cmd.Connection = con;
                        cmd.Parameters.AddWithValue("@id", id);

                        using (SqlDataReader reader = await cmd.ExecuteReaderAsync())
                        {
                            StringBuilder res = new StringBuilder();
                            while (await reader.ReadAsync())
                            {
                                res.Append(reader["userName"]);
                            }
                            return Ok(res.ToString());
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error in DoSqliAsync");
                return StatusCode(500, "An error occurred while processing your request.");
            }
        }
    }
}
