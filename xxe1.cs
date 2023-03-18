
using Microsoft.AspNetCore.Mvc;
using System;
using System.IO;
using System.Xml;
using System.Xml.Schema;

namespace WebFox.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class XxeTest1 : ControllerBase
    {
        [HttpPost]
        public void DoXxe([FromBody] string xmlString)
        {
            XmlDocument xmlDoc = new XmlDocument();
            XmlReaderSettings settings = new XmlReaderSettings();

            settings.DtdProcessing = DtdProcessing.Parse;
            settings.XmlResolver = null;
            settings.ValidationType = ValidationType.Schema;
            settings.ValidationFlags = XmlSchemaValidationFlags.ProcessInlineSchema | XmlSchemaValidationFlags.ProcessSchemaLocation;
            settings.MaxCharactersFromEntities = 0;
            settings.ProhibitDtd = true;
            settings.ValidateInput = true;
            settings.MaxCharactersInDocument = 1024;

            // Additional security setting
            settings.Schemas.XmlResolver = null; // Disable external entities for schema

            XmlReader reader = XmlReader.Create(new StringReader(xmlString), settings);
            xmlDoc.Load(reader);
        }
    }
}
