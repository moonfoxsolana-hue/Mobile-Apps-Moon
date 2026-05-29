package com.mysticnusa.app.ui.components

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import coil.compose.AsyncImage
import com.mysticnusa.app.ui.theme.MysticDarkOverlay

/**
 * A reusable composable that displays a full-screen background image
 * loaded from a remote URL with a semi-transparent dark overlay on top.
 *
 * Place screen content on top of this in a parent Box.
 */
@Composable
fun GameBackground(
    imageUrl: String,
    modifier: Modifier = Modifier
) {
    Box(modifier = modifier.fillMaxSize()) {
        AsyncImage(
            model = imageUrl,
            contentDescription = null,
            contentScale = ContentScale.Crop,
            modifier = Modifier.fillMaxSize()
        )
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(
                    Brush.verticalGradient(
                        colors = listOf(
                            Color.Transparent,
                            MysticDarkOverlay,
                            MysticDarkOverlay
                        )
                    )
                )
        )
    }
}
