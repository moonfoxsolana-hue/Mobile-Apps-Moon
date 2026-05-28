package com.mysticnusa.app

import android.app.Application
import com.mysticnusa.app.data.local.TokenManager
import com.mysticnusa.app.data.remote.RetrofitInstance
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.SupervisorJob
import kotlinx.coroutines.launch

class MysticNusaApp : Application() {

    private val applicationScope = CoroutineScope(SupervisorJob() + Dispatchers.IO)

    override fun onCreate() {
        super.onCreate()
        val tokenManager = TokenManager(this)
        RetrofitInstance.init(tokenManager)
        applicationScope.launch {
            tokenManager.loadCachedToken()
        }
    }
}
