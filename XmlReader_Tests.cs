
using NUnit.Framework;
using System;
using System.Collections.Generic;
using System.IO;
using System.Text;
using System.Xml;

namespace XXEExamples.Tests
{
    [TestFixture]
    public class XmlReader_Tests
    {
        [Test]
        public void XMLReader_WithDTDProcessingProhibited_Safe()
        {
            var exception = Assert.Throws<XmlException>(() =>
            {
                AssertXXE.IsXMLParserSafe((string xml) =>
                {
                    XmlReaderSettings settings = new XmlReaderSettings();
                    settings.DtdProcessing = DtdProcessing.Prohibit;
                    settings.MaxCharactersFromEntities = 1000;
                    
                    // Explicitly set the XmlResolver to a secure resolver with no permissions
                    settings.XmlResolver = new XmlSecureResolver(new XmlUrlResolver(), new System.Security.PermissionSet(System.Security.Permissions.PermissionState.None));
                    
                    settings.MaxCharactersInDocument = 10000;

                    using (MemoryStream stream = new MemoryStream(Encoding.UTF8.GetBytes(xml)))
                    {
                        XmlReader reader = XmlReader.Create(stream, settings);

                        var xmlDocument = new XmlDocument();
                        xmlDocument.XmlResolver = null; // Explicitly set the XmlDocument's resolver to null
                        xmlDocument.Load(reader);
                        return xmlDocument.InnerText;
                    }
                }, true);
            });

            Assert.IsTrue(exception.Message.StartsWith("For security reasons DTD is prohibited in this XML document."));
        }
    }
}
