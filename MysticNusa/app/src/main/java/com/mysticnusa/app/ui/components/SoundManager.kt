package com.mysticnusa.app.ui.components

import android.content.Context
import android.media.MediaPlayer
import android.util.Log

object SoundManager {

    private var bgmPlayer: MediaPlayer? = null
    private var isMuted: Boolean = false

    private const val TAG = "SoundManager"

    /**
     * Plays a one-shot sound effect from a remote URL.
     * The MediaPlayer is released after completion.
     */
    fun playSound(context: Context, url: String) {
        if (isMuted) return
        try {
            val player = MediaPlayer().apply {
                setDataSource(url)
                setOnPreparedListener { it.start() }
                setOnCompletionListener { it.release() }
                setOnErrorListener { mp, _, _ ->
                    Log.e(TAG, "Error playing SFX: $url")
                    mp.release()
                    true
                }
                prepareAsync()
            }
        } catch (e: Exception) {
            Log.e(TAG, "Failed to play sound: $url", e)
        }
    }

    /**
     * Plays looping background music from a remote URL.
     * Stops any currently playing BGM first.
     */
    fun playBgm(context: Context, url: String) {
        stopBgm()
        try {
            bgmPlayer = MediaPlayer().apply {
                setDataSource(url)
                isLooping = true
                setOnPreparedListener { mp ->
                    if (!isMuted) {
                        mp.start()
                    }
                }
                setOnErrorListener { mp, _, _ ->
                    Log.e(TAG, "Error playing BGM: $url")
                    mp.release()
                    bgmPlayer = null
                    true
                }
                prepareAsync()
            }
        } catch (e: Exception) {
            Log.e(TAG, "Failed to play BGM: $url", e)
        }
    }

    /**
     * Stops the currently playing background music and releases resources.
     */
    fun stopBgm() {
        try {
            bgmPlayer?.apply {
                if (isPlaying) stop()
                release()
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error stopping BGM", e)
        }
        bgmPlayer = null
    }

    /**
     * Toggles mute on/off. When muted, SFX won't play and BGM is paused.
     * When unmuted, BGM resumes if it was playing.
     */
    fun toggleMute() {
        isMuted = !isMuted
        try {
            bgmPlayer?.let { player ->
                if (isMuted) {
                    if (player.isPlaying) player.pause()
                } else {
                    player.start()
                }
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error toggling mute", e)
        }
    }
}
