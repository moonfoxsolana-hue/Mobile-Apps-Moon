package com.mysticnusa.app.ui.util

import android.content.Context
import android.media.MediaPlayer
import android.util.Log

class SoundManager(private val context: Context) {
    private var mediaPlayer: MediaPlayer? = null

    private fun playUrl(url: String) {
        try {
            mediaPlayer?.release()
            mediaPlayer = MediaPlayer().apply {
                setDataSource(url)
                setOnPreparedListener { it.start() }
                setOnCompletionListener {
                    it.release()
                    mediaPlayer = null
                }
                setOnErrorListener { mp, _, _ ->
                    mp.release()
                    mediaPlayer = null
                    true
                }
                prepareAsync()
            }
        } catch (e: Exception) {
            Log.e("SoundManager", "Error playing sound: ${e.message}")
        }
    }

    fun playWrongGuess() = playUrl("https://mystical-nusa.web.id/sound/sfx/Babi-Ngepet-game.mp3")
    fun playIntruderWin() = playUrl("https://mystical-nusa.web.id/sound/sfx/Babi-Terbang-win.mp3")
    fun playIntruderLose() = playUrl("https://mystical-nusa.web.id/sound/sfx/Babi-Ngepet-lose.mp3")
    fun playEndSound() = playUrl("https://mystical-nusa.web.id/sound/sfx/end.mp3")

    fun release() {
        mediaPlayer?.release()
        mediaPlayer = null
    }
}
