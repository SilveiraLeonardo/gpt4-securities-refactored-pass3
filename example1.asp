
// Validate user input
int rPort;
const int MinSafePort = 1024;
const int MaxSafePort = 65535;

if (!int.TryParse(Request.get_Item("remotePort"), out rPort) || rPort < MinSafePort || rPort > MaxSafePort)
{
    throw new ValidationException("Invalid input");
}

// Validate the address variable (assuming it comes from user input)
IPAddress address;
string inputAddress = Request.get_Item("address");

if (!IPAddress.TryParse(inputAddress, out address))
{
    throw new ValidationException("Invalid input");
}

// Check against a list of allowed addresses or subnets (example)
List<IPAddressRange> allowedAddresses = new List<IPAddressRange>
{
    new IPAddressRange(IPAddress.Parse("192.168.1.1"), IPAddress.Parse("192.168.1.3")),
};

if(!IsIpAddressAllowed(address, allowedAddresses))
{
    throw new ValidationException("Invalid input");
}

IPEndPoint endpoint = new IPEndPoint(address, rPort);
socket = new Socket(endpoint.AddressFamily, SocketType.Stream, ProtocolType.Tcp);

try
{
    socket.Connect(endpoint);
}
catch (Exception ex)
{
    // Handle connection exceptions here, e.g., log the error and inform the user
    //...
}

// Restrict access to the socket
if (socket.LocalEndPoint.Address.Equals(endpoint.Address) &&
    socket.LocalEndPoint.Port == rPort &&
    socket.RemoteEndPoint.Address.Equals(endpoint.Address) &&
    socket.RemoteEndPoint.Port == rPort &&
    socket.IsBound)
{
    // Validate the socket
    if (socket.IsSecure && socket.Connected)
    {
        // Continue processing
    }
    else
    {
        throw new ValidationException("Invalid input");
    }
}
else
{
    throw new ValidationException("Invalid input");
}

// Custom exception
public class ValidationException : Exception
{
    public ValidationException(string message) : base(message) { }
}

// Custom IPAddressRange class
public class IPAddressRange
{
    public IPAddress LowerBound { get; set; }
    public IPAddress UpperBound { get; set; }

    public IPAddressRange(IPAddress lowerBound, IPAddress upperBound)
    {
        LowerBound = lowerBound;
        UpperBound = upperBound;
    }
}

// Check if IP address is within the allowed range
public bool IsIpAddressAllowed(IPAddress ipAddress, List<IPAddressRange> allowedAddresses)
{
    foreach (IPAddressRange range in allowedAddresses)
    {
        byte[] ipBytes = ipAddress.GetAddressBytes();
        byte[] lowerBytes = range.LowerBound.GetAddressBytes();
        byte[] upperBytes = range.UpperBound.GetAddressBytes();
        
        bool lowerBoundary = true, upperBoundary = true;
        
        for (int i = 0; i < ipBytes.Length && (lowerBoundary || upperBoundary); i++)
        {
            if ((lowerBoundary && ipBytes[i] < lowerBytes[i]) || (upperBoundary && ipBytes[i] > upperBytes[i]))
            {
                return false;
            }
            
            lowerBoundary &= (ipBytes[i] == lowerBytes[i]);
            upperBoundary &= (ipBytes[i] == upperBytes[i]);
        }
        
        if (lowerBoundary || upperBoundary)
        {
            return true;
        }
    }
    
    return false;
}
