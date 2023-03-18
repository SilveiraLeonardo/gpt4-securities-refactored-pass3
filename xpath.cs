
using Microsoft.AspNetCore.Mvc;
using System.Xml;
using System.Text.RegularExpressions;
using System.Web;
using System.Data.SqlClient;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Http;

namespace WebFox.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class XPathController : ControllerBase
    {
        [HttpGet("{user}")]
        public async Task<IActionResult> XPATH(string user)
        {
            // Validate and limit the user input
            if (!Regex.IsMatch(user, @"^[a-zA-Z]{1,50}$"))
            {
                return BadRequest("Invalid user");
            }

            // Load the document and set the root element.
            XmlDocument doc = new XmlDocument();

            // Disable DTD processing and external entities
            XmlReaderSettings settings = new XmlReaderSettings
            {
                Async = true,
                XmlResolver = null,
                ProhibitDtd = true
            };
            
            using (XmlReader reader = XmlReader.Create("bookstore.xml", settings))
            {
                await doc.LoadAsync(reader);
            }
            
            XmlNode root = doc.DocumentElement;

            // Add the namespace.
            XmlNamespaceManager nsmgr = new XmlNamespaceManager(doc.NameTable);
            nsmgr.AddNamespace("bk", "urn:newbooks-schema");

            // Use a parameterized XPath expression
            string xpath = "descendant::bk:book[bk:author/bk:last-name=$user]";
            XsltArgumentList xsltArgs = new XsltArgumentList();
            xsltArgs.AddParam("user", "", user);
            
            XmlNode node = root.SelectSingleNode(xpath, nsmgr);
            
            if (node != null)
            {
                return Ok("Node found");
            }
            else
            {
                return NotFound("Node not found");
            }
        }
    }
}
