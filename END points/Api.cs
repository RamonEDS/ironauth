using System;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;
using System.Security.Principal;
using Newtonsoft.Json;

namespace VoidCorporation
{
    public class AuthData
    {
        public bool Success { get; set; }
        public string Message { get; set; }
        public string Username { get; set; }
        public string HWID { get; set; }
        public string IP { get; set; }
        public double Lat { get; set; }
        public double Lon { get; set; }
        public string ExpiresAt { get; set; }
        public bool IsLifetime { get; set; }
        public int ProductId { get; set; }
    }

    public class ApiAuth
    {
        private readonly string apiUrl;
        private readonly HttpClient httpClient;

        public ApiAuth(string apiUrl)
        {
            this.apiUrl = apiUrl;
            httpClient = new HttpClient();
            httpClient.DefaultRequestHeaders.Add("Accept", "application/json");
        }

        public async Task<AuthData> Login(string username, string password)
        {
            var hwid = GetHWID();
            var data = new
            {
                username,
                password,
                hwid,
                product_id = 1, //ID do produto aqui
                lat = 0.0,
                lon = 0.0
            };

            var json = JsonConvert.SerializeObject(data);
            var content = new StringContent(json, Encoding.UTF8, "application/json");

            try
            {
                var response = await httpClient.PostAsync(apiUrl, content);
                var responseString = await response.Content.ReadAsStringAsync();
                var result = JsonConvert.DeserializeObject<dynamic>(responseString);

                var authData = new AuthData
                {
                    Success = result.success,
                    Message = result.message
                };

                if (authData.Success && result.product_id == 1)
                {
                    authData.Username = result.username;
                    authData.ExpiresAt = result.expires_at;
                    authData.IsLifetime = result.is_lifetime;
                    authData.HWID = hwid;
                    authData.Lat = 0.0;
                    authData.Lon = 0.0;
                    authData.ProductId = result.product_id;
                }
                else if (authData.Success)
                {
                    authData.Success = false;
                    authData.Message = "Produto inválido: não é seu_produtoAqui";
                }

                return authData;
            }
            catch (Exception ex)
            {
                return new AuthData
                {
                    Success = false,
                    Message = $"Erro: {ex.Message}"
                };
            }
        }

        public async Task<bool> ValidateApiKey(string key)
        {
            try
            {
                var response = await httpClient.GetAsync($"{apiUrl}?action=validate_key&key={key}");
                var responseString = await response.Content.ReadAsStringAsync();
                var result = JsonConvert.DeserializeObject<dynamic>(responseString);
                return result.status == "success";
            }
            catch
            {
                return false;
            }
        }

        public string GetHWID()
        {
            try
            {
                using var identity = WindowsIdentity.GetCurrent();
                return identity.User?.Value ?? "Unknown";
            }
            catch
            {
                return "Unknown";
            }
        }

        public string GetClientTimestamp()
        {
            return DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss");
        }
    }
}