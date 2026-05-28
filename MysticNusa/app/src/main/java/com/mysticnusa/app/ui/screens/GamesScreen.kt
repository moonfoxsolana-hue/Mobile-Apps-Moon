package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.navigation.NavController
import com.mysticnusa.app.navigation.Screen
import com.mysticnusa.app.ui.components.BottomNavBar
import com.mysticnusa.app.ui.components.MysticCard
import com.mysticnusa.app.ui.theme.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun GamesScreen(navController: NavController) {
    val games = listOf(
        Triple("\uD83E\uDDE0", "Trivia", "Uji pengetahuanmu!") to Screen.TriviaGame.route,
        Triple("\uD83E\uDDEA", "Logical / IQ Test", "Tes kecerdasan logikamu!") to Screen.LogicalGame.route,
        Triple("\uD83D\uDD2E", "Intuition", "Ikuti intuisimu!") to Screen.IntuitionGame.route,
        Triple("\uD83C\uDCCF", "Tarot Ritual", "Baca takdirmu hari ini") to Screen.TarotGame.route,
        Triple("\uD83C\uDFB2", "Ular Tangga", "Board game klasik multiplayer") to Screen.UlartanggaGame.route,
        Triple("\uD83D\uDC17", "Ngepet Online", "Curi token dari rumah!") to Screen.NgepetGame.route
    )

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Mini Games", color = MysticGold) },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = MaterialTheme.colorScheme.background
                )
            )
        },
        bottomBar = { BottomNavBar(navController) },
        containerColor = MaterialTheme.colorScheme.background
    ) { paddingValues ->
        Column(
            modifier = Modifier
                .padding(paddingValues)
                .fillMaxSize()
                .padding(16.dp)
        ) {
            games.chunked(2).forEach { row ->
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    row.forEach { (info, route) ->
                        val (emoji, title, description) = info
                        MysticCard(
                            modifier = Modifier
                                .weight(1f)
                                .clickable { navController.navigate(route) }
                        ) {
                            Column(
                                modifier = Modifier.padding(16.dp),
                                horizontalAlignment = Alignment.CenterHorizontally
                            ) {
                                Text(text = emoji, fontSize = 36.sp)
                                Spacer(modifier = Modifier.height(8.dp))
                                Text(
                                    text = title,
                                    color = MysticGold,
                                    fontWeight = FontWeight.Bold,
                                    style = MaterialTheme.typography.bodyMedium,
                                    textAlign = TextAlign.Center
                                )
                                Spacer(modifier = Modifier.height(4.dp))
                                Text(
                                    text = description,
                                    color = TextSecondary,
                                    style = MaterialTheme.typography.bodySmall,
                                    textAlign = TextAlign.Center
                                )
                            }
                        }
                    }
                    if (row.size == 1) {
                        Spacer(modifier = Modifier.weight(1f))
                    }
                }
            }
        }
    }
}
