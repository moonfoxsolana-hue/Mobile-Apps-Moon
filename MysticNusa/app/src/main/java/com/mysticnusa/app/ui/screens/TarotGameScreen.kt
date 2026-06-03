package com.mysticnusa.app.ui.screens

import androidx.compose.animation.core.*
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.graphicsLayer
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.SpanStyle
import androidx.compose.ui.text.buildAnnotatedString
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavController
import coil.compose.AsyncImage
import com.mysticnusa.app.data.repository.GamesRepository
import com.mysticnusa.app.ui.components.GameBackground
import com.mysticnusa.app.ui.components.MysticButton
import com.mysticnusa.app.ui.components.MysticTextField
import com.mysticnusa.app.ui.components.SoundManager
import com.mysticnusa.app.ui.theme.*
import com.mysticnusa.app.ui.viewmodels.TarotViewModel

enum class TarotPhase {
    START, CARD_SELECTION, DETAIL_INPUT, ORACLE, READING
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun TarotGameScreen(navController: NavController) {
    val viewModel: TarotViewModel = viewModel(
        factory = TarotViewModel.Factory(GamesRepository())
    )
    val uiState by viewModel.uiState.collectAsState()
    val context = LocalContext.current

    var phase by remember { mutableStateOf(TarotPhase.START) }
    var selectedCardIds by remember { mutableStateOf(setOf<String>()) }
    var name by remember { mutableStateOf("") }
    var energyChoice by remember { mutableStateOf("") }

    val energyOptions = listOf("Api", "Air", "Tanah", "Udara", "Cahaya", "Kegelapan")

    // BGM lifecycle
    LaunchedEffect(Unit) {
        SoundManager.playBgm(context, "https://mystical-nusa.web.id/sound/bgm/bgm-tarot.mp3")
    }
    DisposableEffect(Unit) {
        onDispose {
            SoundManager.stopBgm()
        }
    }

    // Advance phases based on state
    LaunchedEffect(uiState.sessionId, uiState.cards) {
        if (uiState.sessionId != null && uiState.cards.isNotEmpty() && phase == TarotPhase.START) {
            phase = TarotPhase.CARD_SELECTION
        }
    }

    LaunchedEffect(uiState.reading) {
        if (uiState.reading != null) {
            phase = TarotPhase.READING
        }
    }

    // Floating animation for title
    val infiniteTransition = rememberInfiniteTransition(label = "float")
    val floatOffset by infiniteTransition.animateFloat(
        initialValue = 0f,
        targetValue = -8f,
        animationSpec = infiniteRepeatable(
            animation = tween(2000, easing = EaseInOutSine),
            repeatMode = RepeatMode.Reverse
        ),
        label = "floatOffset"
    )

    Box(modifier = Modifier.fillMaxSize()) {
        // Background layer
        GameBackground(
            imageUrl = "https://mystical-nusa.web.id/images/asset/games/background/tarot-background.jpg"
        )

        // Content layer
        Scaffold(
            topBar = {
                TopAppBar(
                    title = { Text("Tarot Ritual", color = TarotYellow) },
                    navigationIcon = {
                        IconButton(onClick = { navController.popBackStack() }) {
                            Icon(Icons.AutoMirrored.Filled.ArrowBack, "Back", tint = TarotYellow)
                        }
                    },
                    colors = TopAppBarDefaults.topAppBarColors(
                        containerColor = Color.Transparent
                    )
                )
            },
            containerColor = Color.Transparent
        ) { paddingValues ->
            Box(modifier = Modifier.padding(paddingValues).fillMaxSize()) {
                if (uiState.isLoading) {
                    Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            CircularProgressIndicator(color = TarotYellow)
                            Spacer(modifier = Modifier.height(12.dp))
                            Text("Membaca energi kosmis...", color = TextSecondary)
                        }
                    }
                } else {
                    when (phase) {
                        TarotPhase.START -> {
                            Column(
                                modifier = Modifier
                                    .fillMaxSize()
                                    .padding(24.dp),
                                horizontalAlignment = Alignment.CenterHorizontally,
                                verticalArrangement = Arrangement.Center
                            ) {
                                Text(
                                    text = "\uD83C\uDCCF",
                                    fontSize = 64.sp,
                                    modifier = Modifier.graphicsLayer {
                                        translationY = floatOffset
                                    }
                                )
                                Spacer(modifier = Modifier.height(16.dp))
                                Text(
                                    text = "Tarot Ritual",
                                    color = TarotYellow,
                                    fontSize = 24.sp,
                                    fontWeight = FontWeight.Bold,
                                    modifier = Modifier.graphicsLayer {
                                        translationY = floatOffset
                                    }
                                )
                                Spacer(modifier = Modifier.height(12.dp))
                                Text(
                                    text = "Baca takdirmu hari ini melalui kartu tarot mistis. Pilih kartu, masukkan energimu, dan terima ramalan dari oracle.",
                                    color = TextSecondary,
                                    textAlign = TextAlign.Center,
                                    style = MaterialTheme.typography.bodyMedium
                                )
                                Spacer(modifier = Modifier.height(32.dp))
                                MysticButton(
                                    text = "Mulai Ritual",
                                    onClick = {
                                        SoundManager.playSound(context, "https://mystical-nusa.web.id/sound/sfx/click2.wav")
                                        viewModel.startRitual()
                                    },
                                    modifier = Modifier.border(1.dp, MysticGold, RoundedCornerShape(8.dp))
                                )

                                uiState.error?.let {
                                    Spacer(modifier = Modifier.height(12.dp))
                                    Text(text = it, color = MaterialTheme.colorScheme.error)
                                }
                            }
                        }

                        TarotPhase.CARD_SELECTION -> {
                            Column(
                                modifier = Modifier
                                    .fillMaxSize()
                                    .verticalScroll(rememberScrollState())
                                    .padding(16.dp),
                                horizontalAlignment = Alignment.CenterHorizontally
                            ) {
                                Text(
                                    text = "Pilih 1-3 Kartu",
                                    color = TarotYellow,
                                    fontSize = 20.sp,
                                    fontWeight = FontWeight.Bold
                                )
                                Spacer(modifier = Modifier.height(8.dp))
                                Text(
                                    text = "Terpilih: ${selectedCardIds.size}/3",
                                    color = TextSecondary
                                )
                                Spacer(modifier = Modifier.height(16.dp))

                                // Card grid - 5 columns x 2 rows
                                uiState.cards.chunked(5).forEach { row ->
                                    Row(
                                        modifier = Modifier.fillMaxWidth(),
                                        horizontalArrangement = Arrangement.spacedBy(6.dp)
                                    ) {
                                        row.forEach { card ->
                                            val isSelected = card.id in selectedCardIds
                                            Card(
                                                modifier = Modifier
                                                    .weight(1f)
                                                    .aspectRatio(0.65f)
                                                    .clickable {
                                                        SoundManager.playSound(context, "https://mystical-nusa.web.id/sound/sfx/click2.wav")
                                                        selectedCardIds = if (isSelected) {
                                                            selectedCardIds - card.id
                                                        } else if (selectedCardIds.size < 3) {
                                                            selectedCardIds + card.id
                                                        } else {
                                                            selectedCardIds
                                                        }
                                                    }
                                                    .then(
                                                        if (isSelected) Modifier.border(2.dp, MysticPurple, RoundedCornerShape(8.dp))
                                                        else Modifier
                                                    ),
                                                shape = RoundedCornerShape(8.dp),
                                                colors = CardDefaults.cardColors(
                                                    containerColor = Color.Transparent
                                                )
                                            ) {
                                                Box(
                                                    modifier = Modifier.fillMaxSize(),
                                                    contentAlignment = Alignment.Center
                                                ) {
                                                    AsyncImage(
                                                        model = "https://mystical-nusa.web.id/images/asset/tarot/closed-card.png",
                                                        contentDescription = "Tarot Card",
                                                        contentScale = ContentScale.Crop,
                                                        modifier = Modifier
                                                            .fillMaxSize()
                                                            .clip(RoundedCornerShape(8.dp))
                                                    )
                                                }
                                            }
                                        }
                                    }
                                    Spacer(modifier = Modifier.height(6.dp))
                                }

                                Spacer(modifier = Modifier.height(24.dp))

                                if (selectedCardIds.isNotEmpty()) {
                                    MysticButton(
                                        text = "Lanjut",
                                        onClick = {
                                            SoundManager.playSound(context, "https://mystical-nusa.web.id/sound/sfx/click2.wav")
                                            phase = TarotPhase.DETAIL_INPUT
                                        }
                                    )
                                }
                            }
                        }

                        TarotPhase.DETAIL_INPUT -> {
                            Column(
                                modifier = Modifier
                                    .fillMaxSize()
                                    .verticalScroll(rememberScrollState())
                                    .padding(24.dp),
                                horizontalAlignment = Alignment.CenterHorizontally
                            ) {
                                Text(
                                    text = "Masukkan Detail",
                                    color = TarotYellow,
                                    fontSize = 20.sp,
                                    fontWeight = FontWeight.Bold
                                )
                                Spacer(modifier = Modifier.height(24.dp))

                                MysticTextField(
                                    value = name,
                                    onValueChange = { name = it },
                                    label = "Nama"
                                )

                                Spacer(modifier = Modifier.height(16.dp))

                                Text("Pilih Energi:", color = TextSecondary)
                                Spacer(modifier = Modifier.height(8.dp))

                                Column {
                                    energyOptions.chunked(3).forEach { row ->
                                        Row(
                                            modifier = Modifier.fillMaxWidth(),
                                            horizontalArrangement = Arrangement.spacedBy(8.dp)
                                        ) {
                                            row.forEach { energy ->
                                                val isSelected = energyChoice == energy
                                                FilterChip(
                                                    selected = isSelected,
                                                    onClick = {
                                                        SoundManager.playSound(context, "https://mystical-nusa.web.id/sound/sfx/click2.wav")
                                                        energyChoice = energy
                                                    },
                                                    label = {
                                                        Text(
                                                            energy,
                                                            color = if (isSelected) TarotYellow else TextSecondary
                                                        )
                                                    },
                                                    colors = FilterChipDefaults.filterChipColors(
                                                        selectedContainerColor = MysticPurple.copy(alpha = 0.3f),
                                                        selectedLabelColor = TarotYellow
                                                    ),
                                                    modifier = Modifier
                                                        .weight(1f)
                                                        .border(
                                                            1.dp,
                                                            if (isSelected) MysticGold else MysticGold.copy(alpha = 0.5f),
                                                            RoundedCornerShape(8.dp)
                                                        )
                                                )
                                            }
                                        }
                                        Spacer(modifier = Modifier.height(4.dp))
                                    }
                                }

                                Spacer(modifier = Modifier.height(24.dp))

                                MysticButton(
                                    text = "Kirim",
                                    onClick = {
                                        SoundManager.playSound(context, "https://mystical-nusa.web.id/sound/sfx/click2.wav")
                                        val cardSelections = uiState.cards
                                            .filter { it.id in selectedCardIds }
                                            .map { com.mysticnusa.app.data.models.TarotCardSelection(it.id, it.orientation ?: "upright") }
                                        viewModel.pickCards(
                                            name.ifBlank { null },
                                            energyChoice.ifBlank { null },
                                            cardSelections
                                        )
                                        phase = TarotPhase.ORACLE
                                    },
                                    enabled = name.isNotBlank()
                                )
                            }
                        }

                        TarotPhase.ORACLE -> {
                            Column(
                                modifier = Modifier
                                    .fillMaxSize()
                                    .verticalScroll(rememberScrollState())
                                    .padding(24.dp),
                                horizontalAlignment = Alignment.CenterHorizontally,
                                verticalArrangement = Arrangement.Center
                            ) {
                                Text("\uD83C\uDCCF", fontSize = 48.sp)
                                Spacer(modifier = Modifier.height(16.dp))

                                // Oracle info
                                uiState.oracleName?.let { oracle ->
                                    Text(
                                        text = oracle,
                                        color = MysticPurpleLight,
                                        fontSize = 18.sp,
                                        fontWeight = FontWeight.SemiBold
                                    )
                                    Spacer(modifier = Modifier.height(8.dp))
                                }
                                uiState.oracleMessage?.let { message ->
                                    Text(
                                        text = "\"$message\"",
                                        color = TextSecondary,
                                        style = MaterialTheme.typography.bodyMedium.copy(
                                            fontStyle = androidx.compose.ui.text.font.FontStyle.Italic
                                        ),
                                        textAlign = TextAlign.Center
                                    )
                                    Spacer(modifier = Modifier.height(16.dp))
                                }

                                Text(
                                    text = "Kartu Terpilih",
                                    color = TarotYellow,
                                    fontSize = 20.sp,
                                    fontWeight = FontWeight.Bold
                                )
                                Spacer(modifier = Modifier.height(12.dp))
                                Text(
                                    text = "${selectedCardIds.size} kartu telah dipilih",
                                    color = TextSecondary
                                )
                                Spacer(modifier = Modifier.height(24.dp))

                                Row(
                                    modifier = Modifier.fillMaxWidth(),
                                    horizontalArrangement = Arrangement.Center
                                ) {
                                    uiState.cardDetails.forEach { card ->
                                        Card(
                                            modifier = Modifier
                                                .width(100.dp)
                                                .padding(horizontal = 4.dp)
                                                .border(
                                                    1.dp,
                                                    MysticGold.copy(alpha = 0.5f),
                                                    RoundedCornerShape(8.dp)
                                                ),
                                            shape = RoundedCornerShape(8.dp),
                                            colors = CardDefaults.cardColors(
                                                containerColor = MysticDarkOverlay
                                            )
                                        ) {
                                            Column(
                                                modifier = Modifier.padding(8.dp),
                                                horizontalAlignment = Alignment.CenterHorizontally
                                            ) {
                                                // Show card image if available
                                                card.image?.let { imageUrl ->
                                                    val fullUrl = if (imageUrl.startsWith("/")) {
                                                        "https://mystical-nusa.web.id$imageUrl"
                                                    } else {
                                                        imageUrl
                                                    }
                                                    AsyncImage(
                                                        model = fullUrl,
                                                        contentDescription = card.name ?: "Tarot Card",
                                                        contentScale = ContentScale.Fit,
                                                        modifier = Modifier
                                                            .height(120.dp)
                                                            .clip(RoundedCornerShape(4.dp))
                                                            .border(
                                                                1.dp,
                                                                MysticGold,
                                                                RoundedCornerShape(4.dp)
                                                            )
                                                    )
                                                    Spacer(modifier = Modifier.height(8.dp))
                                                }
                                                Text(
                                                    text = card.name ?: "Kartu",
                                                    color = TarotYellow,
                                                    fontWeight = FontWeight.Bold,
                                                    fontSize = 12.sp,
                                                    textAlign = TextAlign.Center,
                                                    maxLines = 1
                                                )
                                                Text(
                                                    text = card.orientation ?: "",
                                                    color = MysticPurpleLight,
                                                    style = MaterialTheme.typography.labelSmall,
                                                    fontSize = 10.sp
                                                )
                                            }
                                        }
                                    }
                                }

                                Spacer(modifier = Modifier.height(24.dp))

                                MysticButton(
                                    text = "Minta Ramalan AI",
                                    onClick = {
                                        SoundManager.playSound(context, "https://mystical-nusa.web.id/sound/sfx/click2.wav")
                                        viewModel.getReading()
                                    }
                                )

                                uiState.error?.let {
                                    Spacer(modifier = Modifier.height(12.dp))
                                    Text(text = it, color = MaterialTheme.colorScheme.error)
                                }
                            }
                        }

                        TarotPhase.READING -> {
                            Column(
                                modifier = Modifier
                                    .fillMaxSize()
                                    .verticalScroll(rememberScrollState())
                                    .padding(16.dp),
                                horizontalAlignment = Alignment.CenterHorizontally
                            ) {
                                Text("\u2728", fontSize = 48.sp)
                                Spacer(modifier = Modifier.height(12.dp))
                                Text(
                                    text = "Ramalan Tarot",
                                    color = TarotYellow,
                                    fontSize = 22.sp,
                                    fontWeight = FontWeight.Bold
                                )
                                Spacer(modifier = Modifier.height(16.dp))

                                Row(
                                    modifier = Modifier.fillMaxWidth(),
                                    horizontalArrangement = Arrangement.Center
                                ) {
                                    uiState.cardDetails.forEach { card ->
                                        Card(
                                            modifier = Modifier
                                                .width(100.dp)
                                                .padding(horizontal = 4.dp)
                                                .border(
                                                    1.dp,
                                                    MysticGold.copy(alpha = 0.5f),
                                                    RoundedCornerShape(8.dp)
                                                ),
                                            shape = RoundedCornerShape(8.dp),
                                            colors = CardDefaults.cardColors(
                                                containerColor = MysticDarkOverlay
                                            )
                                        ) {
                                            Column(
                                                modifier = Modifier.padding(8.dp),
                                                horizontalAlignment = Alignment.CenterHorizontally
                                            ) {
                                                // Show card image if available
                                                card.image?.let { imageUrl ->
                                                    val fullUrl = if (imageUrl.startsWith("/")) {
                                                        "https://mystical-nusa.web.id$imageUrl"
                                                    } else {
                                                        imageUrl
                                                    }
                                                    AsyncImage(
                                                        model = fullUrl,
                                                        contentDescription = card.name ?: "Tarot Card",
                                                        contentScale = ContentScale.Fit,
                                                        modifier = Modifier
                                                            .height(120.dp)
                                                            .clip(RoundedCornerShape(4.dp))
                                                            .border(
                                                                1.dp,
                                                                MysticGold,
                                                                RoundedCornerShape(4.dp)
                                                            )
                                                    )
                                                    Spacer(modifier = Modifier.height(8.dp))
                                                }
                                                Text(
                                                    text = card.name ?: "",
                                                    color = TarotYellow,
                                                    fontWeight = FontWeight.Bold,
                                                    fontSize = 12.sp,
                                                    textAlign = TextAlign.Center,
                                                    maxLines = 1
                                                )
                                                Surface(
                                                    shape = RoundedCornerShape(4.dp),
                                                    color = MysticPurple.copy(alpha = 0.3f)
                                                ) {
                                                    Text(
                                                        text = card.orientation ?: "",
                                                        color = MysticPurpleLight,
                                                        style = MaterialTheme.typography.labelSmall,
                                                        fontSize = 10.sp,
                                                        modifier = Modifier.padding(
                                                            horizontal = 4.dp,
                                                            vertical = 1.dp
                                                        )
                                                    )
                                                }
                                            }
                                        }
                                    }
                                }

                                Spacer(modifier = Modifier.height(16.dp))

                                // AI Reading section with mystic styling
                                Card(
                                    modifier = Modifier
                                        .fillMaxWidth()
                                        .border(1.dp, MysticGold.copy(alpha = 0.5f), RoundedCornerShape(12.dp)),
                                    shape = RoundedCornerShape(12.dp),
                                    colors = CardDefaults.cardColors(
                                        containerColor = MysticDarkOverlay
                                    )
                                ) {
                                    Text(
                                        text = formatTarotReading(uiState.reading ?: ""),
                                        color = Color(0xFFfffde7),
                                        style = MaterialTheme.typography.bodyMedium,
                                        modifier = Modifier.padding(16.dp)
                                    )
                                }

                                Spacer(modifier = Modifier.height(24.dp))

                                MysticButton(
                                    text = "Ritual Baru",
                                    onClick = {
                                        SoundManager.playSound(context, "https://mystical-nusa.web.id/sound/sfx/click2.wav")
                                        phase = TarotPhase.START
                                        selectedCardIds = emptySet()
                                        name = ""
                                        energyChoice = ""
                                        viewModel.startRitual()
                                    }
                                )
                            }
                        }
                    }
                }
            }
        }
    }
}

private fun formatTarotReading(text: String): androidx.compose.ui.text.AnnotatedString {
    val blueColor = Color(0xFF81D4FA) // Light Blue
    val yellowColor = Color(0xFFFFF59D) // Light Yellow

    return buildAnnotatedString {
        val pattern = """(\*\*.*?\*\*|\*.*?\*)""".toRegex()
        var lastIndex = 0

        pattern.findAll(text).forEach { match ->
            append(text.substring(lastIndex, match.range.first))

            val matchValue = match.value
            if (matchValue.startsWith("**") && matchValue.endsWith("**")) {
                val content = matchValue.substring(2, matchValue.length - 2)
                pushStyle(SpanStyle(color = blueColor, fontWeight = FontWeight.Bold))
                append(content)
                pop()
            } else if (matchValue.startsWith("*") && matchValue.endsWith("*")) {
                val content = matchValue.substring(1, matchValue.length - 1)
                pushStyle(SpanStyle(color = yellowColor))
                append(content)
                pop()
            }

            lastIndex = match.range.last + 1
        }
        append(text.substring(lastIndex))
    }
}
