document.addEventListener("DOMContentLoaded",async  function () {
   await iniciarJogo();

  document
    .getElementById("campoMinado")
    .addEventListener("click", function (event) {
      var cell = event.target;
      if (cell.tagName.toLowerCase() === "td") {
        var linha = cell.parentNode.rowIndex;
        var coluna = cell.cellIndex;
        processarJogada(linha, coluna);
      }
    });

  document
    .getElementById("btnReiniciar")
    .addEventListener("click", async function () {
     await processarReiniciar();
    });

  atualizarTabela();
});

function renderizarJogo(matriz) {
  var tabela = document.getElementById("campoMinado");
  tabela.innerHTML = "";

  for (var i = 0; i < matriz.length; i++) {
    var linha = document.createElement("tr");

    for (var j = 0; j < matriz[i].length; j++) {
      var celula = document.createElement("td");
      celula.textContent = matriz[i][j] == "*" ? " " : matriz[i][j];
      linha.appendChild(celula);
    }

    tabela.appendChild(linha);
  }
}

async function processarReiniciar() {
    try {
      const response = await fetch("campoMinado.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: "reiniciar=true",
      });
  
      if (!response.ok) {
        throw new Error("Erro ao reiniciar o jogo.");
      }
  
      const estadoJogo = await response.json();
      renderizarJogo(estadoJogo.matriz);
    } catch (error) {
      console.error(error.message);
    }
  }
async function reiniciarJogo() {
  try {
    const response = await fetch("campoMinado.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "reiniciar=1",
    });

    if (!response.ok) {
      throw new Error("Erro ao reiniciar o jogo.");
    }

    const estadoJogo = await response.json();
    iniciarJogo();
    renderizarJogo(estadoJogo.matriz);
  } catch (error) {
    console.error(error.message);
  }
}

async function iniciarJogo() {
    try {
      const response = await fetch("campoMinado.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
      });
  
      if (!response.ok) {
        throw new Error("Erro ao iniciar o jogo.");
      }
  
      const estadoJogo = await response.json();
      renderizarJogo(estadoJogo.matriz);
    } catch (error) {
      console.error(error.message);
    }
  }

  async function atualizarTabela() {
    try {
      const response = await fetch("campoMinado.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
      });
  
      if (!response.ok) {
        throw new Error("Erro ao obter o estado do jogo.");
      }
  
      const estadoJogo = await response.json();
      if (estadoJogo.jogo_encerrado) {
        alert(estadoJogo.mensagem);
      }
      renderizarJogo(estadoJogo.matriz);
    } catch (error) {
      console.error(error.message);
    }
  }

async function processarJogada(linha, coluna) {
  try {
    const response = await fetch("campoMinado.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `linha=${linha}&coluna=${coluna}`,
    });

    if (!response.ok) {
      throw new Error("Erro ao processar a jogada.");
    }

    const result = await response.json();

    if (result.mensagem) {
      alert(result.mensagem);
    }

    renderizarJogo(result.matriz);
  } catch (error) {
    console.error(error.message);
  }
}
