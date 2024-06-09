<?php

namespace App\Domain\Agents\Factories;

use App\Domain\Agents\Enums\Agent;
use App\Domain\Agents\Interfaces\AgentInterface;
use App\Domain\Agents\TheParaphraser;

class AgentFactory
{
    public function make(Agent $agent): AgentInterface
    {
        return match ($agent) {
            Agent::THE_PARAPHRASER => new TheParaphraser($agent),
        };
    }
}