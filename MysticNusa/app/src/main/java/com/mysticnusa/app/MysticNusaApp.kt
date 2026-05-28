package com.mysticnusa.app

import android.app.Application
import com.mysticnusa.app.data.local.TokenManager
import com.mysticnusa.app.data.remote.RetrofitInstance

class MysticNusaApp : Application() {

    override fun onCreate() {
        super.onCreate()
        val tokenManager = TokenManager(this)
        RetrofitInstance.init(tokenManager)
    }
}
