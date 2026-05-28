package com.mysticnusa.app.data.repository

import com.mysticnusa.app.data.models.*
import com.mysticnusa.app.data.remote.RetrofitInstance

class AirdropRepository {

    private val api = RetrofitInstance.api

    suspend fun claimFirst(): Result<AirdropClaimResponse> {
        return try {
            val response = api.claimAirdrop()
            if (response.isSuccessful) {
                response.body()?.let {
                    Result.success(it)
                } ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Claim failed: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun claimWithCode(code: String): Result<AirdropClaimResponse> {
        return try {
            val response = api.claimWithCode(AirdropCodeRequest(code))
            if (response.isSuccessful) {
                response.body()?.let {
                    Result.success(it)
                } ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Code claim failed: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }
}
