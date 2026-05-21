#Este é o formato do payload para a criacao de uma contribuicao.
Mas, depois da conclusão do módulo de autenticacao o trace_id nao será mais inserido pelo user, ele será captado automaticamente do auth()->id()

{
  "user_id": "544e26f1-1fa6-4ce2-a902-12f63fde25ba",
  "group_id": "3a15c573-efe3-4ce0-a3f4-77ef5507b5e0",
  "wallet_code": "TEST_001",
  "group_cycle_id": "a91b5b60-8d19-4fd0-8321-0aa9010dad9f",
  "trace_id": "544e26f1-1fa6-4ce2-a902-12f63fde25ba",
  "amount": 500.00,
  "phone_number": "840000001",
  "status": "active",
  "saving_date": "2025-05-14",
  "social_amount": 50.00,
  "description": "Poupança teste"
}