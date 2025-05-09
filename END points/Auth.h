#pragma once
#include <string>

struct AuthData {
    bool success = false;
    std::string message;
    std::string username;
    std::string hwid;
    std::string ip;
    double lat;
    double lon;
    std::string expiresAt;
    bool isLifetime;
    int product_id = 0;
};

class ApiAuth {
private:
    std::string apiUrl_;

public:
    ApiAuth(const std::string& apiUrl);
    AuthData Login(const std::string& username, const std::string& password);
    bool ValidateApiKey(const std::string& key);
    std::string getHWID();
    std::string getClientTimestamp(); 

private:
    
};