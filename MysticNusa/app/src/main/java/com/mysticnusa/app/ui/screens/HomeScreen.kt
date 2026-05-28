package com.mysticnusa.app.ui.screens

import android.content.Intent
import android.net.Uri
import androidx.compose.animation.AnimatedVisibility
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ExpandLess
import androidx.compose.material.icons.filled.ExpandMore
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.navigation.NavController
import com.mysticnusa.app.navigation.Screen
import com.mysticnusa.app.ui.components.BottomNavBar
import com.mysticnusa.app.ui.components.MysticButton
import com.mysticnusa.app.ui.components.MysticCard
import com.mysticnusa.app.ui.theme.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun HomeScreen(navController: NavController) {
    Scaffold(
        bottomBar = { BottomNavBar(navController) },
        containerColor = MaterialTheme.colorScheme.background
    ) { paddingValues ->
        Column(
            modifier = Modifier
                .padding(paddingValues)
                .verticalScroll(rememberScrollState())
                .fillMaxSize()
        ) {
            HeroSection(navController)
            AboutSection()
            TokenomicsSection()
            UtilitySection()
            RoadmapSection()
            FaqSection()
            CommunitySection()
            PartnershipSection()
            FooterSection()
        }
    }
}

@Composable
private fun HeroSection(navController: NavController) {
    Column(
        modifier = Modifier
            .fillMaxWidth()
            .padding(24.dp),
        horizontalAlignment = Alignment.CenterHorizontally
    ) {
        Text(
            text = "Mystic Nusa - \$MYNU",
            fontSize = 28.sp,
            fontWeight = FontWeight.Bold,
            color = MysticGold,
            textAlign = TextAlign.Center
        )
        Spacer(modifier = Modifier.height(12.dp))
        Text(
            text = "Legenda Desa Mistis - Antara Kekayaan dan Kutukan.",
            style = MaterialTheme.typography.bodyLarge,
            color = TextSecondary,
            textAlign = TextAlign.Center
        )
        Spacer(modifier = Modifier.height(12.dp))
        Text(
            text = "Mystic Nusa adalah proyek token berbasis komunitas yang terinspirasi dari legenda desa mistis Nusantara. Dibangun di atas jaringan Solana, proyek ini menggabungkan narasi budaya dengan utilitas kripto modern.",
            style = MaterialTheme.typography.bodyMedium,
            color = TextSecondary,
            textAlign = TextAlign.Center
        )
        Spacer(modifier = Modifier.height(24.dp))
        MysticButton(
            text = "Klaim Airdrop",
            onClick = { navController.navigate(Screen.Claim.route) }
        )
    }
}

@Composable
private fun AboutSection() {
    Column(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp, vertical = 24.dp)
    ) {
        SectionTitle("Apa itu Mystic Nusa?")
        Spacer(modifier = Modifier.height(12.dp))
        Text(
            text = "Mystic Nusa (\$MYNU) adalah token komunitas yang dibangun di atas blockchain Solana. Terinspirasi dari cerita rakyat dan legenda desa mistis Nusantara, proyek ini membawa narasi budaya ke dunia kripto.",
            color = TextSecondary,
            style = MaterialTheme.typography.bodyMedium
        )
        Spacer(modifier = Modifier.height(16.dp))

        val bulletPoints = listOf(
            "100% komunitas, tanpa VC, tanpa presale",
            "Utility: Trading, Staking, GameFi, Akses eksklusif, DAO, Swap merchandise",
            "Berdiri di atas kepercayaan komunitas"
        )
        bulletPoints.forEach { point ->
            Row(modifier = Modifier.padding(vertical = 4.dp)) {
                Text("  \u2022  ", color = MysticGold)
                Text(text = point, color = TextSecondary, style = MaterialTheme.typography.bodyMedium)
            }
        }

        Spacer(modifier = Modifier.height(16.dp))
        Text(
            text = "Kenapa unik?",
            fontWeight = FontWeight.SemiBold,
            color = MysticGold,
            style = MaterialTheme.typography.bodyLarge
        )
        Spacer(modifier = Modifier.height(4.dp))
        Text(
            text = "Mystic Nusa menggabungkan storytelling budaya Nusantara dengan teknologi blockchain. Bukan sekadar token, tapi sebuah ekosistem dengan cerita yang hidup.",
            color = TextSecondary,
            style = MaterialTheme.typography.bodyMedium
        )

        Spacer(modifier = Modifier.height(16.dp))
        Text(
            text = "Apa Tujuannya?",
            fontWeight = FontWeight.SemiBold,
            color = MysticGold,
            style = MaterialTheme.typography.bodyLarge
        )
        Spacer(modifier = Modifier.height(4.dp))
        Text(
            text = "Membangun komunitas kripto yang kuat dengan fondasi budaya lokal, memberikan utilitas nyata kepada pemegang token melalui staking, games, dan akses eksklusif.",
            color = TextSecondary,
            style = MaterialTheme.typography.bodyMedium
        )
    }
}

@Composable
private fun TokenomicsSection() {
    Column(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp, vertical = 24.dp)
    ) {
        SectionTitle("Distribusi Token \$MYNU")
        Spacer(modifier = Modifier.height(16.dp))

        val tokenDistribution = listOf(
            "Airdrop Komunitas" to "20%",
            "Staking Reward" to "10%",
            "Reward Holder" to "15%",
            "Mini Games" to "5%",
            "Likuiditas DEX & CEX" to "15%",
            "Ekosistem" to "10%",
            "Marketing" to "10%",
            "Tim" to "10%",
            "Treasury" to "5%"
        )

        MysticCard {
            Column(modifier = Modifier.padding(16.dp)) {
                tokenDistribution.forEach { (category, allocation) ->
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(vertical = 6.dp),
                        horizontalArrangement = Arrangement.SpaceBetween
                    ) {
                        Text(text = category, color = TextSecondary, style = MaterialTheme.typography.bodyMedium)
                        Text(text = allocation, color = MysticGold, fontWeight = FontWeight.Bold)
                    }
                    if (category != "Treasury") {
                        Divider(color = MysticPurple.copy(alpha = 0.3f))
                    }
                }
            }
        }

        Spacer(modifier = Modifier.height(12.dp))
        Text(
            text = "Total Supply: 400.000.000 \$MYNU",
            color = MysticGold,
            fontWeight = FontWeight.SemiBold,
            style = MaterialTheme.typography.bodyMedium
        )
        Text(
            text = "Network: Solana SPL Token",
            color = TextSecondary,
            style = MaterialTheme.typography.bodySmall
        )
    }
}

@Composable
private fun UtilitySection() {
    Column(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp, vertical = 24.dp)
    ) {
        SectionTitle("Utilitas Token")
        Spacer(modifier = Modifier.height(16.dp))

        val utilities = listOf(
            Triple("\uD83D\uDCC8", "Trading", "Perdagangan token di DEX dan CEX terkemuka"),
            Triple("\uD83D\uDD12", "Staking", "Stake token dan dapatkan reward harian"),
            Triple("\uD83C\uDFAE", "GameFi", "Mainkan mini games dan dapatkan token"),
            Triple("\uD83D\uDD11", "Akses Eksklusif", "Akses konten dan fitur premium"),
            Triple("\uD83D\uDDF3\uFE0F", "DAO Voting", "Suarakan pendapatmu dalam governance"),
            Triple("\uD83D\uDECD\uFE0F", "Merch Swap", "Tukar token dengan merchandise eksklusif")
        )

        Column {
            utilities.chunked(2).forEach { row ->
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    row.forEach { (emoji, title, desc) ->
                        MysticCard(modifier = Modifier.weight(1f)) {
                            Column(
                                modifier = Modifier.padding(12.dp),
                                horizontalAlignment = Alignment.CenterHorizontally
                            ) {
                                Text(text = emoji, fontSize = 28.sp)
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
                                    text = desc,
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

@Composable
private fun RoadmapSection() {
    Column(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp, vertical = 24.dp)
    ) {
        SectionTitle("Roadmap 2025-2026")
        Spacer(modifier = Modifier.height(16.dp))

        val roadmap = listOf(
            "Q2 2025" to listOf("Launch Token \$MYNU", "Airdrop Komunitas", "Website & Sosial Media", "Mini Games v1"),
            "Q3 2025" to listOf("Staking Platform", "Listing DEX", "Partnership", "Community Growth"),
            "Q4 2025" to listOf("GameFi Expansion", "DAO Governance", "NFT Collection", "CEX Listing"),
            "Q1 2026" to listOf("Mobile App", "Merch Store", "Cross-chain Bridge", "Ecosystem Fund"),
            "Q2 2026" to listOf("Metaverse Integration", "Advanced GameFi", "Global Expansion", "Launchpad"),
            "Q3 2026" to listOf("DeFi Suite", "Enterprise Partners", "Real World Assets", "Full DAO")
        )

        Column {
            roadmap.chunked(2).forEach { row ->
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    row.forEach { (quarter, milestones) ->
                        MysticCard(modifier = Modifier.weight(1f)) {
                            Column(modifier = Modifier.padding(12.dp)) {
                                Text(
                                    text = quarter,
                                    color = MysticGold,
                                    fontWeight = FontWeight.Bold,
                                    style = MaterialTheme.typography.bodyMedium
                                )
                                Spacer(modifier = Modifier.height(8.dp))
                                milestones.forEach { milestone ->
                                    Text(
                                        text = "\u2022 $milestone",
                                        color = TextSecondary,
                                        style = MaterialTheme.typography.bodySmall,
                                        modifier = Modifier.padding(vertical = 2.dp)
                                    )
                                }
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

@Composable
private fun FaqSection() {
    Column(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp, vertical = 24.dp)
    ) {
        SectionTitle("FAQ - Tanya Jawab")
        Spacer(modifier = Modifier.height(16.dp))

        val faqs = listOf(
            "Apa itu Mystic Nusa?" to "Mystic Nusa adalah proyek token komunitas berbasis Solana yang terinspirasi dari legenda desa mistis Nusantara.",
            "Bagaimana cara mendapatkan MYNU?" to "Kamu bisa mendapatkan token MYNU melalui airdrop, staking rewards, mini games, atau membeli di DEX.",
            "Apa kegunaan token MYNU?" to "Token MYNU dapat digunakan untuk trading, staking, bermain games, akses konten eksklusif, voting DAO, dan menukar merchandise.",
            "Dimana saya bisa dapatkan kode Airdrop?" to "Kode airdrop dibagikan melalui channel komunitas resmi kami di Telegram, Twitter, dan YouTube.",
            "Kenapa saya harus mendaftar?" to "Pendaftaran diperlukan untuk melacak token yang kamu dapatkan dan memastikan distribusi yang adil.",
            "Apa itu temporary token?" to "Temporary token adalah token yang belum bisa ditarik sampai token resmi diluncurkan di blockchain Solana.",
            "Apakah token ini sudah bisa diperdagangkan?" to "Token saat ini dalam fase pre-launch. Perdagangan akan tersedia setelah listing di DEX pada Q3 2025.",
            "Apakah proyek ini aman?" to "Ya, proyek ini dibangun oleh komunitas dengan transparansi penuh. Smart contract akan diaudit sebelum listing."
        )

        faqs.forEach { (question, answer) ->
            FaqItem(question = question, answer = answer)
        }
    }
}

@Composable
private fun FaqItem(question: String, answer: String) {
    var expanded by remember { mutableStateOf(false) }

    MysticCard(
        modifier = Modifier
            .fillMaxWidth()
            .clickable { expanded = !expanded }
    ) {
        Column(modifier = Modifier.padding(16.dp)) {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = question,
                    color = MysticGold,
                    fontWeight = FontWeight.Medium,
                    style = MaterialTheme.typography.bodyMedium,
                    modifier = Modifier.weight(1f)
                )
                Icon(
                    imageVector = if (expanded) Icons.Default.ExpandLess else Icons.Default.ExpandMore,
                    contentDescription = "Toggle",
                    tint = MysticGold
                )
            }
            AnimatedVisibility(visible = expanded) {
                Text(
                    text = answer,
                    color = TextSecondary,
                    style = MaterialTheme.typography.bodySmall,
                    modifier = Modifier.padding(top = 8.dp)
                )
            }
        }
    }
}

@Composable
private fun CommunitySection() {
    val context = LocalContext.current

    Column(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp, vertical = 24.dp),
        horizontalAlignment = Alignment.CenterHorizontally
    ) {
        SectionTitle("Gabung Komunitas Mystic Nusa")
        Spacer(modifier = Modifier.height(16.dp))

        val socials = listOf(
            "\uD83D\uDCFA YouTube" to "https://youtube.com/@MysticNusa",
            "\uD83D\uDC26 Twitter" to "https://twitter.com/MysticNusa",
            "\uD83D\uDCF7 Instagram" to "https://instagram.com/MysticNusa",
            "\u2708\uFE0F Telegram" to "https://t.me/MysticNusa",
            "\uD83C\uDFB5 TikTok" to "https://tiktok.com/@MysticNusa"
        )

        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceEvenly
        ) {
            socials.forEach { (label, url) ->
                TextButton(onClick = {
                    val intent = Intent(Intent.ACTION_VIEW, Uri.parse(url))
                    context.startActivity(intent)
                }) {
                    Text(
                        text = label.split(" ").first(),
                        fontSize = 20.sp
                    )
                }
            }
        }

        Spacer(modifier = Modifier.height(8.dp))

        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceEvenly
        ) {
            socials.forEach { (label, url) ->
                TextButton(onClick = {
                    val intent = Intent(Intent.ACTION_VIEW, Uri.parse(url))
                    context.startActivity(intent)
                }) {
                    Text(
                        text = label.split(" ").last(),
                        color = TextSecondary,
                        style = MaterialTheme.typography.labelSmall
                    )
                }
            }
        }
    }
}

@Composable
private fun PartnershipSection() {
    Column(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp, vertical = 24.dp),
        horizontalAlignment = Alignment.CenterHorizontally
    ) {
        SectionTitle("Partnership")
        Spacer(modifier = Modifier.height(12.dp))
        Text(
            text = "Kami terbuka untuk kolaborasi dengan proyek, komunitas, dan brand yang sejalan dengan visi Mystic Nusa.",
            color = TextSecondary,
            style = MaterialTheme.typography.bodyMedium,
            textAlign = TextAlign.Center
        )
    }
}

@Composable
private fun FooterSection() {
    Box(
        modifier = Modifier
            .fillMaxWidth()
            .padding(16.dp),
        contentAlignment = Alignment.Center
    ) {
        Text(
            text = "\u00A9 2025 Mystic Nusa. All rights reserved.",
            color = TextSecondary.copy(alpha = 0.6f),
            style = MaterialTheme.typography.bodySmall
        )
    }
}

@Composable
private fun SectionTitle(text: String) {
    Text(
        text = text,
        fontSize = 22.sp,
        fontWeight = FontWeight.Bold,
        color = MysticGold
    )
}
