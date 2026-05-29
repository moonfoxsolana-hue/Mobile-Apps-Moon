package com.mysticnusa.app.data.remote

import com.mysticnusa.app.BuildConfig
import com.mysticnusa.app.data.local.TokenManager
import okhttp3.Interceptor
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.OkHttpClient
import okhttp3.Protocol
import okhttp3.Response
import okhttp3.ResponseBody.Companion.toResponseBody
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit
import javax.net.ssl.SSLContext
import javax.net.ssl.TrustManager
import javax.net.ssl.X509TrustManager

object RetrofitInstance {

    private const val BASE_URL = "https://mystical-nusa.web.id/api/"
    const val IMAGE_BASE_URL = "https://mystical-nusa.web.id/"

    var tokenManager: TokenManager? = null
        private set

    fun init(tokenManager: TokenManager) {
        this.tokenManager = tokenManager
    }

    private val authInterceptor = Interceptor { chain ->
        val token = tokenManager?.cachedToken
        val request = chain.request().newBuilder().apply {
            token?.let {
                addHeader("Authorization", "Bearer $it")
            }
            addHeader("Accept", "application/json")
            addHeader("Content-Type", "application/json")
        }.build()
        try {
            chain.proceed(request)
        } catch (e: Exception) {
            // Return a fake error response instead of crashing
            Response.Builder()
                .request(request)
                .protocol(Protocol.HTTP_1_1)
                .code(599)
                .message(e.message ?: "Network error")
                .body("{\"message\":\"${e.message ?: "Koneksi gagal"}\"}".toResponseBody("application/json".toMediaType()))
                .build()
        }
    }

    private val unauthorizedInterceptor = Interceptor { chain ->
        val response = chain.proceed(chain.request())
        if (response.code == 401) {
            tokenManager?.clearCachedToken()
        }
        response
    }

    private val loggingInterceptor = HttpLoggingInterceptor().apply {
        level = HttpLoggingInterceptor.Level.BODY
    }

    private val okHttpClient: OkHttpClient by lazy {
        val clientBuilder = OkHttpClient.Builder()
            .addInterceptor(authInterceptor)
            .addInterceptor(unauthorizedInterceptor)
            .connectTimeout(30, TimeUnit.SECONDS)
            .readTimeout(30, TimeUnit.SECONDS)
            .writeTimeout(30, TimeUnit.SECONDS)

        // Trust all certificates for development only (handles Let's Encrypt on older devices)
        if (BuildConfig.DEBUG) {
            try {
                val trustAllCerts = arrayOf<TrustManager>(object : X509TrustManager {
                    override fun checkClientTrusted(chain: Array<java.security.cert.X509Certificate>, authType: String) {}
                    override fun checkServerTrusted(chain: Array<java.security.cert.X509Certificate>, authType: String) {}
                    override fun getAcceptedIssuers(): Array<java.security.cert.X509Certificate> = arrayOf()
                })
                val sslContext = SSLContext.getInstance("TLS")
                sslContext.init(null, trustAllCerts, java.security.SecureRandom())
                clientBuilder.sslSocketFactory(sslContext.socketFactory, trustAllCerts[0] as X509TrustManager)
                clientBuilder.hostnameVerifier { _, _ -> true }
            } catch (e: Exception) {
                // If SSL setup fails, just use default
            }
        }

        if (BuildConfig.DEBUG) {
            clientBuilder.addInterceptor(loggingInterceptor)
        }

        clientBuilder.build()
    }

    val api: ApiService by lazy {
        Retrofit.Builder()
            .baseUrl(BASE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(ApiService::class.java)
    }
}
