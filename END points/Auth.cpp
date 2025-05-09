#include "Auth.h"
#include "json.hpp"
#include <windows.h>
#include <sstream>
#include <curl.h>
#include <atlsecurity.h>
#include <iomanip>
#include <chrono>
#include <iostream>
#include <ntstatus.h>

#pragma comment(lib, "advapi32.lib")
#pragma comment(lib, "Ws2_32.lib")
#pragma comment(lib, "ntdll.lib")
#pragma comment(lib, "crypt32.lib")
#pragma comment(lib, "wldap32.lib")
#pragma comment(lib, "normaliz.lib")
#pragma comment(lib, "libcurl.lib")

using json = nlohmann::json;

ApiAuth::ApiAuth(const std::string& apiUrl) : apiUrl_(apiUrl) {}

static size_t WriteCallback(void* contents, size_t size, size_t nmemb, std::string* s) {
    size_t newLength = size * nmemb;
    try {
        s->append((char*)contents, newLength);
    }
    catch (const std::exception& e) {
        return 0;
    }
    return newLength;
}

AuthData ApiAuth::Login(const std::string& username, const std::string& password) {
    CURL* curl;
    CURLcode res;
    std::string response_string;
    AuthData result;
    std::string hwid = getHWID();
    if (hwid.empty())
        hwid = getHWID();

    curl = curl_easy_init();
    if (!curl) {
        result.message = "Erro ao inicializar curl";
        return result;
    }

    std::string url = apiUrl_;

    double lat = 0.0, lon = 0.0;
    std::string city = "Desconhecido";

    
    json data = {
        {"username", username},
        {"password", password},
        {"hwid", hwid},
        {"product_id", 1}, // Aqui voce configura o ID do seu produto, exemplo ID 1, e replica isso em todos os seus produtos só adicionar outros ID na sequencia
        {"lat", lat},
        {"lon", lon}
    };
    std::string json_str = data.dump();

    curl_easy_setopt(curl, CURLOPT_URL, url.c_str());
    curl_easy_setopt(curl, CURLOPT_POST, 1L);
    curl_easy_setopt(curl, CURLOPT_POSTFIELDS, json_str.c_str());
    curl_easy_setopt(curl, CURLOPT_POSTFIELDSIZE, json_str.length());

    struct curl_slist* headers = nullptr;
    headers = curl_slist_append(headers, "Content-Type: application/json");
    if (!headers) {
        result.message = "Erro ao criar cabecalhos curl";
        curl_easy_cleanup(curl);
        return result;
    }
    curl_easy_setopt(curl, CURLOPT_HTTPHEADER, headers);

    curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, WriteCallback);
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &response_string);

    res = curl_easy_perform(curl);

    curl_slist_free_all(headers);
    curl_easy_cleanup(curl);

    if (res != CURLE_OK) {
        result.message = "Erro na requisi��o: " + std::string(curl_easy_strerror(res));
        return result;
    }

    try {
        json response = json::parse(response_string);
        if (response.contains("success") && response["success"].get<bool>()) {
          
            if (response.contains("product_id") && response["product_id"].get<int>() == 1) {
                result.success = true;
                result.message = response["message"].get<std::string>();
                result.username = response["username"].get<std::string>();
                result.expiresAt = response["expires_at"].get<std::string>();
                result.isLifetime = response["is_lifetime"].get<bool>();
                result.hwid = hwid;
                result.lat = lat;
                result.lon = lon;
                result.product_id = response["product_id"].get<int>(); 
            }
            else {
                result.success = false;
                result.message = "Produto inv�lido: n�o � Void_trick";
            }
        }
        else {
            result.success = false;
            result.message = response["message"].get<std::string>();
        }
    }
    catch (const json::exception& e) {
        result.success = false;
        result.message = "Erro ao parsear JSON: " + std::string(e.what());
        if (response_string.find("<!DOCTYPE") != std::string::npos || response_string.find("<html") != std::string::npos) {
            result.message += " (Resposta cont�m HTML, poss�vel erro no servidor)";
        }
    }

    std::cout << "Login response: " << response_string << std::endl;
    std::cout << "Result: success=" << result.success << ", message=" << result.message
        << ", username=" << result.username << ", expiresAt=" << result.expiresAt
        << ", product_id=" << result.product_id << std::endl;

    return result;
}

bool ApiAuth::ValidateApiKey(const std::string& key) {
    CURL* curl;
    CURLcode res;
    std::string response_string;

    curl = curl_easy_init();
    if (!curl) {
        return false;
    }

    std::string url = apiUrl_ + "?action=validate_key&key=" + key;

    curl_easy_setopt(curl, CURLOPT_URL, url.c_str());
    curl_easy_setopt(curl, CURLOPT_HTTPGET, 1L);
    curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, WriteCallback);
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &response_string);

    res = curl_easy_perform(curl);

    curl_easy_cleanup(curl);

    if (res != CURLE_OK) {
        return false;
    }

    try {
        json response = json::parse(response_string);
        if (response.contains("status")) {
            return response["status"] == "success";
        }
        return false;
    }
    catch (const json::exception& e) {
        return false;
    }
}

std::string ApiAuth::getHWID() {
    ATL::CAccessToken accessToken;
    ATL::CSid currentUserSid;
    if (accessToken.GetProcessToken(TOKEN_READ | TOKEN_QUERY) &&
        accessToken.GetUser(&currentUserSid))
        return std::string(CT2A(currentUserSid.Sid()));
    return "Unknown";
}

std::string ApiAuth::getClientTimestamp() {
    auto now = std::chrono::system_clock::now();
    auto time = std::chrono::system_clock::to_time_t(now);
    std::stringstream ss;
    ss << std::put_time(std::localtime(&time), "%Y-%m-%d %H:%M:%S");
    return ss.str();
}