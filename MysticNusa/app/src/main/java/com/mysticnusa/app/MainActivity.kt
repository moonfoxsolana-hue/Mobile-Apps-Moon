package com.mysticnusa.app

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.material3.Surface
import androidx.compose.material3.MaterialTheme
import androidx.compose.ui.Modifier
import androidx.navigation.compose.rememberNavController
import com.mysticnusa.app.data.local.TokenManager
import com.mysticnusa.app.navigation.MysticNavGraph
import com.mysticnusa.app.ui.theme.MysticNusaTheme

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        val tokenManager = TokenManager(applicationContext)

        setContent {
            MysticNusaTheme {
                Surface(
                    modifier = Modifier.fillMaxSize(),
                    color = MaterialTheme.colorScheme.background
                ) {
                    val navController = rememberNavController()
                    MysticNavGraph(
                        navController = navController,
                        tokenManager = tokenManager
                    )
                }
            }
        }
    }
}
