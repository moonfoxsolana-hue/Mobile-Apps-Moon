package com.mysticnusa.app.data.repository

import com.google.gson.Gson
import com.google.gson.JsonObject
import com.mysticnusa.app.data.models.*
import com.mysticnusa.app.data.remote.RetrofitInstance

class AuthRepository {

    private val api = RetrofitInstance.api

    private fun parseErrorMessage(errorBody: String?, fallback: String): String {
        if (errorBody.isNullOrBlank()) return fallback
        return try {
            val json = Gson().fromJson(errorBody, JsonObject::class.java)
            json.get("message")?.asString ?: fallback
        } catch (e: Exception) {
            fallback
        }
    }

    suspend fun register(request: RegisterRequest): Result<AuthResponse> {
        return try {
            val response = api.register(request)
            if (response.isSuccessful) {
                response.body()?.let {
                    Result.success(it)
                } ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Registration failed: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun login(request: LoginRequest): Result<AuthResponse> {
        return try {
            val response = api.login(request)
            if (response.isSuccessful) {
                response.body()?.let {
                    Result.success(it)
                } ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Login failed: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun logout(): Result<ApiResponse> {
        return try {
            val response = api.logout()
            if (response.isSuccessful) {
                response.body()?.let {
                    Result.success(it)
                } ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Logout failed: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun updateWallet(walletAddress: String): Result<ApiResponse> {
        return try {
            val response = api.updateWallet(WalletRequest(walletAddress))
            if (response.isSuccessful) {
                response.body()?.let {
                    Result.success(it)
                } ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Wallet update failed: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }
}
