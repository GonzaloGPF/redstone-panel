/** What the Minecraft server says about itself (see MinecraftServerInterface). Only address and checkedAt are there when it is offline. */
export type ServerStatus = {
  online: boolean
  address: string
  latency: number | null
  motd: string | null
  version: string | null
  protocol: number | null
  players: number | null
  maxPlayers: number | null
  world: string | null
  gameMode: string | null
  checkedAt: string
}
